<?php

namespace App\Admin\InterviewManagement\Controllers;

use App\Admin\InterviewManagement\Queries\InterviewIndexQuery;
use App\Admin\InterviewManagement\Resources\InterviewReportResource;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\Models\Answer;
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
            ->addSelect([
                'avg_score' => Answer::query()
                    ->whereColumn('interviews.id','interview_answers.interview_id')
                    ->groupBy('interview_id')
                    ->selectRaw("SUM('score') as avg_score")
            ])
            ->orderByDesc('avg_score')
            ->when(request()->filled('filter.status') && request()->enum('filter.status', InterviewStatusEnum::class) == InterviewStatusEnum::Accepted,
                fn(Builder $builder) => $builder->take(5)
            )
            ->when(request()->isNotFilled('filter.status'),fn(Builder $builder) => $builder->skip(5))
            ->with('candidate')
            ->paginate()
            ->through(fn(Interview $interview) => new InterviewReportValueObject($interview));

        return InterviewReportResource::collection($finishedInterviews);
    }
}
