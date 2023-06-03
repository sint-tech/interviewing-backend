<?php

namespace App\Admin\Organization\Queries;

use Domain\Organization\Models\Organization;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

class IndexOrganizationQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = Organization::query();

        parent::__construct($subject, $request);

        $this->allowedFilters(
            $this->getAllowedFilters()
        );

        $this->allowedIncludes(
            $this->getAllowedIncludes()
        );
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::exact('id'),
            AllowedFilter::partial('name'),
        ];
    }

    protected function getAllowedIncludes(): array
    {
        return [
            AllowedInclude::relationship('currentManager', 'oldestManager'),
            AllowedInclude::relationship('employees'),
        ];
    }
}
