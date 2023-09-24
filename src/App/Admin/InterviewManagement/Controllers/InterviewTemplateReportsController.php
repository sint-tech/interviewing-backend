<?php

namespace App\Admin\InterviewManagement\Controllers;

use App\Admin\InterviewManagement\Resources\InterviewReportResource;
use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\InterviewManagement\ValueObjects\InterviewReportValueObject;
use Support\Controllers\Controller;

class InterviewTemplateReportsController extends Controller
{
    public function __invoke(InterviewTemplate $interview_template)
    {
        $finishedInterviews = $interview_template
            ->finishedInterviews()
            ->withWhereHas('defaultLastReport')
            ->with('candidate')
            ->get()->map(fn(Interview $interview) => new InterviewReportValueObject($interview));

        return InterviewReportResource::collection($finishedInterviews);
    }
}
