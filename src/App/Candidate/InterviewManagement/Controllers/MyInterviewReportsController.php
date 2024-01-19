<?php

namespace App\Candidate\InterviewManagement\Controllers;

use App\Candidate\InterviewManagement\Resources\InterviewReportResource;
use Domain\ReportManagement\Models\InterviewReport;
use Support\Controllers\Controller;

class MyInterviewReportsController extends Controller
{
    public function __invoke()
    {
        return InterviewReportResource::collection(
            InterviewReport::query()->paginate(pagination_per_page())
        );
    }
}
