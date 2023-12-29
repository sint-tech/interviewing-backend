<?php

namespace App\Admin\Organization\Queries;

use Domain\Organization\Models\Employee;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class IndexEmployeeQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = Employee::query();

        parent::__construct($subject, $request);

        $this->defaultSorts('-id');

        $this->allowedFilters(
            AllowedFilter::exact('organization_id'),
            AllowedFilter::exact('id'),
            AllowedFilter::exact('is_organization_manager'),
            AllowedFilter::partial('first_name'),
            AllowedFilter::partial('last_name'),
        );

        $this->allowedSorts(
            AllowedSort::field('id'),
            AllowedSort::field('created_at'),
            AllowedSort::field('organization_id'),
        );
    }
}
