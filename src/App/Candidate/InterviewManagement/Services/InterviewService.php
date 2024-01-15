<?php

namespace App\Candidate\InterviewManagement\Services;

use App\Candidate\InterviewManagement\Requests\StartInterviewRequest;
use App\Exceptions\InterviewReachedMaxConnectionTriesException;
use Domain\InterviewManagement\Actions\ContinueInterviewAction;
use Domain\InterviewManagement\Actions\CreateInterviewAction;
use Domain\InterviewManagement\Actions\InterviewReachedMaxConnectionTriesAction;
use Domain\InterviewManagement\DataTransferObjects\InterviewDto;
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
        protected MarkInvitationAsExpiredAction $markInvitationAsExpiredAction
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
        return $this->createInterviewAction->execute(InterviewDto::from([
            'candidate_id' => auth()->id(),
            'vacancy_id' => $request->vacancy()->id,
            'interview_template_id' => $request->interviewTemplate()->id,
            'started_at' => Carbon::now(),
        ]));
    }

    /**
     * @throws InterviewReachedMaxConnectionTriesException
     */
    public function continueInterview(Interview $interview): Interview
    {
        if ($this->interviewReachedMaxConnectionTriesAction->execute($interview)) {
            $this->setInvitationAsExpired($interview);
            throw new InterviewReachedMaxConnectionTriesException();
        }

        if ($interview->allQuestionsAnswered()) {
            $this->setInvitationAsExpired($interview);
        }

        return $this->continueInterviewAction->execute($interview);
    }

    protected function setInvitationAsExpired(Interview $interview): bool
    {
        $invitation = Invitation::query()->firstWhere([
            'vacancy_id' => $interview->vacancy->id,
            'candidate_id' => $interview->candidate->id,
        ]);

        if ($invitation) {
            return $this->markInvitationAsExpiredAction->execute($invitation);
        }

        return false;
    }
}
