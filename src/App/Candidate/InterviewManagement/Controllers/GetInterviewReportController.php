<?php

namespace App\Candidate\InterviewManagement\Controllers;

use App\Candidate\InterviewManagement\Resources\InterviewReportResource;
use Domain\InterviewManagement\Models\Interview;
use Support\Controllers\Controller;

class GetInterviewReportController extends Controller
{
    public function __invoke(Interview $interview)
    {
        $report = $interview->defaultLastReport()
            ->withEndedVacancy()
            ->firstOrFail();

        return InterviewReportResource::make(
            $report->load('reportable.vacancy.organization')
        );
    }
}
