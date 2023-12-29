<?php

namespace App\Admin\Organization\Controllers;

use App\Admin\Organization\Queries\IndexEmployeeQuery;
use App\Admin\Organization\Requests\EmployeeStoreRequest;
use App\Admin\Organization\Requests\EmployeeUpdateRequest;
use App\Admin\Organization\Resources\EmployeeResource;
use Domain\Organization\Actions\CreateEmployeeAction;
use Domain\Organization\Actions\UpdateEmployeeAction;
use Domain\Organization\DataTransferObjects\EmployeeData;
use Domain\Organization\Models\Employee;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class EmployeeController extends Controller
{
    public function index(IndexEmployeeQuery $query): AnonymousResourceCollection
    {
        return EmployeeResource::collection(
            $query->paginate(pagination_per_page())
        );
    }

    public function show(Employee $employee): EmployeeResource
    {
        return EmployeeResource::make($employee);
    }

    public function store(EmployeeStoreRequest $request, CreateEmployeeAction $action): EmployeeResource
    {
        return EmployeeResource::make(
            $action->execute(
                EmployeeData::from($request->safe()->toArray())
            )
        );
    }

    public function update(Employee $employee, EmployeeUpdateRequest $request, UpdateEmployeeAction $action): EmployeeResource
    {
        return EmployeeResource::make(
            $action->execute($employee, EmployeeData::from(array_merge($employee->attributesToArray(), $request->safe()->toArray())))
        );
    }

    public function destroy(Employee $employee): EmployeeResource
    {
        $employee->delete();

        return EmployeeResource::make($employee);
    }
}
