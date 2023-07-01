<?php

namespace App\Candidate\JobTitle\Queries;

use Domain\JobTitle\Models\JobTitle;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class JobTitleIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = JobTitle::query();

        parent::__construct($subject, $request);

        $this->allowedFilters(
            $this->getAllowedFilters()
        );
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::exact('availability_status'),
            AllowedFilter::exact('id'),
            AllowedFilter::partial('title'),
        ];
    }
}
