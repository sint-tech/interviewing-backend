<?php

namespace App\Admin\InterviewManagement\Controllers;

use App\Admin\InterviewManagement\Queries\GetInterviewsReportsQuery;
use App\Admin\InterviewManagement\Resources\InterviewReportResource;
use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\ValueObjects\InterviewReportValueObject;
use Support\Controllers\Controller;

class GetInterviewsReportsController extends Controller
{
    public function __invoke(GetInterviewsReportsQuery $query)
    {
        $finishedInterviews = $query
            ->withWhereHas('defaultLastReport')
            ->with('candidate')
            ->paginate(pagination_per_page());

        return InterviewReportResource::collection($finishedInterviews);
    }
}
