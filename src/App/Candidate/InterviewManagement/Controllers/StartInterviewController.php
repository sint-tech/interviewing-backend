<?php

namespace App\Candidate\InterviewManagement\Controllers;

use App\Candidate\InterviewManagement\Requests\StartInterviewRequest;
use App\Candidate\InterviewManagement\Resources\StartedInterviewResource;
use Domain\InterviewManagement\Actions\CreateInterviewAction;
use Domain\InterviewManagement\DataTransferObjects\InterviewDto;
use Illuminate\Support\Carbon;
use Support\Controllers\Controller;

class StartInterviewController extends Controller
{
    public function __invoke(StartInterviewRequest $request, CreateInterviewAction $action): StartedInterviewResource
    {
        $interview = $action->execute(InterviewDto::from([
            'candidate_id' => auth()->id(),
            'vacancy_id' => $request->vacancy()->getKey(),
            'interview_template_id' => $request->interviewTemplate()->getKey(),
            'started_at' => Carbon::now(),
        ]));

        return StartedInterviewResource::make($interview);
    }
}
