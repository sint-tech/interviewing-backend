<?php

namespace App\Candidate\InterviewManagement\Services;

use App\Candidate\InterviewManagement\Requests\StartInterviewRequest;
use App\Exceptions\InterviewReachedMaxConnectionTriesException;
use Domain\InterviewManagement\Actions\ContinueInterviewAction;
use Domain\InterviewManagement\Actions\CreateInterviewAction;
use Domain\InterviewManagement\Actions\HasRunningInterviewsForCandidateAction;
use Domain\InterviewManagement\Actions\InterviewReachedMaxConnectionTriesAction;
use Domain\InterviewManagement\DataTransferObjects\InterviewDto;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\Models\Interview;
use Domain\Invitation\Actions\MarkInvitationAsExpiredAction;
use Domain\Invitation\Models\Invitation;
use Illuminate\Support\Carbon;

class InterviewService
{
    public function __construct(
        protected CreateInterviewAction $createInterviewAction,
        protected ContinueInterviewAction $continueInterviewAction,
        protected InterviewReachedMaxConnectionTriesAction $interviewReachedMaxConnectionTriesAction,
        protected MarkInvitationAsExpiredAction $markInvitationAsExpiredAction,
        protected HasRunningInterviewsForCandidateAction $hasRunningInterviewsForCandidateAction
    ) {
    }

    public function continueOrStartInterview(StartInterviewRequest $request): Interview
    {
        $interview = Interview::query()->where([
            'vacancy_id' => $request->vacancy()->getKey(),
            'interview_template_id' => $request->interviewTemplate()->getKey(),
        ])->whereRunning()->first();

        if ($interview instanceof Interview) {
            return $this->continueInterview($interview);
        }

        return $this->startInterview($request);
    }

    public function startInterview(StartInterviewRequest $request): Interview
    {
        auth()->user()->runningInterviews()
            ->get()->each(function (Interview $interview) {
                $this->withdrewInterview($interview);
                $this->setInterviewInvitationsAsExpired($interview);
            });

        $interview = $this->createInterviewAction->execute(InterviewDto::from([
            'candidate_id' => auth()->id(),
            'vacancy_id' => $request->vacancy()->id,
            'interview_template_id' => $request->interviewTemplate()->id,
            'started_at' => Carbon::now(),
        ]));

        //mark invitations as expired when max connection tries is 0 or 1
        if ($this->interviewReachedMaxConnectionTriesAction->execute($interview)) {
            $this->setInterviewInvitationsAsExpired($interview);
        }

        return $interview;
    }

    /**
     * @throws InterviewReachedMaxConnectionTriesException
     */
    public function continueInterview(Interview $interview): Interview
    {
        if ($this->interviewReachedMaxConnectionTriesAction->execute($interview)) {
            $this->withdrewInterview($interview);
            $this->setInterviewInvitationsAsExpired($interview);
            throw new InterviewReachedMaxConnectionTriesException();
        }

        if ($interview->allQuestionsAnswered()) {
            $this->setInterviewInvitationsAsExpired($interview);
        }

        return $this->continueInterviewAction->execute($interview);
    }

    protected function setInterviewInvitationsAsExpired(Interview $interview): bool
    {
        $invitations = Invitation::query()->where([
            'vacancy_id' => $interview->vacancy->id,
            'email' => $interview->candidate->email,
        ])->get();

        if ($invitations->isEmpty()) {
            return false;
        }

        $invitations->each(fn (Invitation $invitation) => $this->markInvitationAsExpiredAction->execute($invitation));

        return true;
    }

    protected function withdrewInterview(Interview $interview): bool
    {
        return $interview->update([
            'status' => InterviewStatusEnum::Withdrew,
            'ended_at' => now(),
        ]); //todo make action to change status
    }
}
