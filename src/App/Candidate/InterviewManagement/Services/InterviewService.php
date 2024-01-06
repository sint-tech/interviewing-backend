<?php

namespace App\Candidate\InterviewManagement\Services;

use App\Candidate\InterviewManagement\Requests\StartInterviewRequest;
use Domain\InterviewManagement\Actions\ContinueInterviewAction;
use Domain\InterviewManagement\Actions\CreateInterviewAction;
use Domain\InterviewManagement\Actions\InterviewReachedMaxConnectionTriesAction;
use Domain\InterviewManagement\DataTransferObjects\InterviewDto;
use Domain\InterviewManagement\Models\Interview;
use Illuminate\Support\Carbon;

class InterviewService
{
    public function __construct(
        protected CreateInterviewAction $createInterviewAction,
        protected ContinueInterviewAction $continueInterviewAction,
        protected InterviewReachedMaxConnectionTriesAction $connectionTriesAction
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
            'vacancy_id' => $request->vacancy()->getKey(),
            'interview_template_id' => $request->interviewTemplate()->getKey(),
            'started_at' => Carbon::now(),
        ]));
    }

    public function continueInterview(Interview $interview): Interview
    {
        return $this->continueInterviewAction->execute($interview);
    }
}
