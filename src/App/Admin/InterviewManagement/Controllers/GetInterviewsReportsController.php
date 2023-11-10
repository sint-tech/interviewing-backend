<?php

namespace App\Admin\InterviewManagement\Controllers;

use App\Admin\InterviewManagement\Queries\GetInterviewsReportsQuery;
use App\Admin\InterviewManagement\Resources\InterviewReportResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class GetInterviewsReportsController extends Controller
{
    public function __invoke(GetInterviewsReportsQuery $query): AnonymousResourceCollection
    {
        $finishedInterviews = $query
            ->withWhereHas('defaultLastReport')
            ->with('candidate')
            ->paginate(pagination_per_page());

        return InterviewReportResource::collection($finishedInterviews);
    }
}
