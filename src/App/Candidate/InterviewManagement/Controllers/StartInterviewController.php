<?php

namespace App\Candidate\InterviewManagement\Controllers;

use App\Candidate\InterviewManagement\Requests\StartInterviewRequest;
use App\Candidate\InterviewManagement\Resources\StartedInterviewResource;
use App\Candidate\InterviewManagement\Services\InterviewService;
use Domain\InterviewManagement\Actions\CreateInterviewAction;
use Support\Controllers\Controller;

class StartInterviewController extends Controller
{
    public function __construct(protected InterviewService $interviewService)
    {
    }

    public function __invoke(StartInterviewRequest $request, CreateInterviewAction $action): StartedInterviewResource
    {
        return StartedInterviewResource::make(
            $this->interviewService->continueOrStartInterview($request)
        );
    }
}
