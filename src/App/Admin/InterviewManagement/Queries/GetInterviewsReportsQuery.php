<?php

namespace App\Admin\InterviewManagement\Queries;

use Domain\InterviewManagement\Models\Interview;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetInterviewsReportsQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = Interview::query();

        parent::__construct($subject, $request);

        $this->allowedFilters(
            AllowedFilter::exact('interview_template_id'),
            AllowedFilter::exact('vacancy_id')
        );
    }
}
