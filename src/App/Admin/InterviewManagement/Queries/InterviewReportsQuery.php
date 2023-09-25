<?php

namespace App\Admin\InterviewManagement\Queries;

use Domain\InterviewManagement\Models\Interview;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class InterviewReportsQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = Interview::query();

        parent::__construct($subject, $request);

        $this->withWhereHas('defaultLastReport');

        $this->allowedFilters(
            AllowedFilter::exact('interview_template_id')
        );
    }
}
