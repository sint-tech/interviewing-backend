<?php

namespace App\Organization\EmployeeManagement\Queries;

use Domain\Organization\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class IndexEmployeesQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = Employee::query()->forUser($request->user());

        parent::__construct($subject, $request);

        $this->allowedFilters([
            AllowedFilter::exact('id'),
            AllowedFilter::partial('first_name'),
            AllowedFilter::partial('last_name'),
            AllowedFilter::callback(
                'by_name',
                fn (Builder $builder, $value) => $builder
                    ->where('first_name', 'LIKE', "%$value%")
                    ->orWhere('last_name', 'LIKE', "%$value%")
            ),
        ]);
    }
}
