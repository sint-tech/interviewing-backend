<?php

namespace App\Admin\InterviewManagement\Queries;

use Domain\InterviewManagement\Models\InterviewTemplate;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

class InterviewTemplateIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = InterviewTemplate::query();

        parent::__construct($subject, $request);

        $this->allowedFilters(
            AllowedFilter::exact('id'),
            AllowedFilter::exact('availability_status')
        );

        $this->allowedIncludes(
            AllowedInclude::relationship('questionVariants'),
        );

        $this->defaultSort('-id');
    }
}
