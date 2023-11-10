<?php

namespace App\Admin\Vacancy\Queries;

use Domain\Vacancy\Models\Vacancy;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

class VacancyQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = Vacancy::query();

        parent::__construct($subject, $request);

        $this->allowedFilters(
            AllowedFilter::exact('interview_template_id'),
            AllowedFilter::exact('id'),
        );

        $this->allowedIncludes(
            AllowedInclude::relationship('defaultInterviewTemplate')
        );

        $this->defaultSort('-id');
    }
}
