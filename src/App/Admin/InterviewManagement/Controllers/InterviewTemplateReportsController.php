<?php

namespace App\Admin\InterviewManagement\Controllers;

use App\Admin\InterviewManagement\Queries\InterviewIndexQuery;
use App\Admin\InterviewManagement\Resources\InterviewReportResource;
use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\InterviewManagement\ValueObjects\InterviewReportValueObject;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class InterviewTemplateReportsController extends Controller
{
    public function __invoke(InterviewTemplate $interview_template,InterviewIndexQuery $query): AnonymousResourceCollection
    {
        $finishedInterviews = $interview_template
            ->interviews()
            ->setQuery($query->toBase())
            ->withWhereHas('defaultLastReport')
            ->with('candidate')
            ->paginate()
            ->through(fn(Interview $interview) => new InterviewReportValueObject($interview));

        return InterviewReportResource::collection($finishedInterviews);
    }
}
