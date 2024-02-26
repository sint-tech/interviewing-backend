<?php

namespace App\Organization\InterviewManagement\Controllers;

use App\Organization\InterviewManagement\Queries\GetInterviewReportQuery;
use App\Organization\InterviewManagement\Resources\InterviewReportResource;
use Domain\InterviewManagement\Models\Interview;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class InterviewsReportsController extends Controller
{
    public function index(GetInterviewReportQuery $query): AnonymousResourceCollection
    {
        $finishedInterviews = $query
            ->withWhereHas('defaultLastReport')
            ->with('candidate')
            ->paginate(pagination_per_page());

        return InterviewReportResource::collection($finishedInterviews);
    }

    public function show(Interview $interview): InterviewReportResource
    {
        return InterviewReportResource::make($interview->load('candidate'));
    }
}
