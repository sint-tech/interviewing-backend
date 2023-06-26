<?php

namespace App\Website\InterviewManagement\Controllers;

use App\Website\InterviewManagement\Factories\InterviewDataFactory;
use App\Website\InterviewManagement\Resources\StartedInterviewResource;
use Domain\InterviewManagement\Actions\CreateInterviewAction;
use Domain\InterviewManagement\DataTransferObjects\InterviewDto;
use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Illuminate\Support\Carbon;
use Support\Controllers\Controller;

class StartInterviewController extends Controller
{
    public function __invoke(int $interview_template): StartedInterviewResource
    {
        $interview = (new CreateInterviewAction(
            InterviewDto::from([
                'candidate_id' => auth()->id(),
                'interview_template_id' => InterviewTemplate::query()->findOrFail($interview_template)->getKey(),
                'started_at'            => Carbon::now(),
            ])
        ))->execute();

        return StartedInterviewResource::make($interview);
    }
}
