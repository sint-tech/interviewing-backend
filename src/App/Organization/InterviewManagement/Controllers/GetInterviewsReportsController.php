<?php

namespace App\Organization\InterviewManagement\Controllers;

use App\Organization\InterviewManagement\Queries\GetInterviewReportQuery;
use App\Organization\InterviewManagement\Resources\InterviewReportResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class GetInterviewsReportsController extends Controller
{
    public function __invoke(GetInterviewReportQuery $query): AnonymousResourceCollection
    {
        $finishedInterviews = $query
            ->withWhereHas('defaultLastReport')
            ->with('candidate')
            ->paginate(pagination_per_page());

        return InterviewReportResource::collection($finishedInterviews);
    }
}
