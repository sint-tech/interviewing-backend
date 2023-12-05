<?php

namespace App\Candidate\InterviewManagement\Queries;

use Domain\InterviewManagement\Models\Interview;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class MyInterviewsQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = Interview::query();

        parent::__construct($subject, $request);

        $this->allowedFilters(
            $this->getAllowedFilters()
        );

        $this->allowedIncludes(
            $this->getAllowedIncludes()
        );

        $this->allowedSorts(
            AllowedSort::field('created_at')
        );

        $this->defaultSort('-id');
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::exact('id'),
        ];
    }

    protected function getAllowedIncludes(): array
    {
        return [
            AllowedInclude::relationship('answers'),
            AllowedInclude::relationship('answers.questionVariant'),
        ];
    }
}
