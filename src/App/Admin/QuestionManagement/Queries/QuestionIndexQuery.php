<?php

namespace App\Admin\QuestionManagement\Queries;

use Domain\QuestionManagement\Models\Question;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

class QuestionIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = Question::query();

        parent::__construct($subject, $request);

        $this->allowedFilters(
            $this->getAllowedFilters()
        );

        $this->defaultSort('-updated_at');

        $this->allowedSorts('id','created_at','updated_at');

        $this->allowedIncludes(
            AllowedInclude::relationship('questionCluster')
        );
    }

    public function getAllowedFilters(): array
    {
        return [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('question_cluster_id'),
            AllowedFilter::exact('question_type', 'question_type'),
            AllowedFilter::partial('title'),
            AllowedFilter::partial('description'),
            AllowedFilter::exact('min_reading_time', 'min_reading_duration_in_seconds'),
            AllowedFilter::exact('min_reading_time', 'max_reading_duration_in_seconds'),
            AllowedFilter::exact('difficult_level'),
        ];
    }
}
