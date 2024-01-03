<?php

namespace App\Organization\EmployeeManagement\Controllers;

use App\Organization\EmployeeManagement\Queries\IndexEmployeesQuery;
use App\Organization\EmployeeManagement\Requests\EmployeeStoreRequest;
use App\Organization\EmployeeManagement\Requests\EmployeeUpdateRequest;
use App\Organization\EmployeeManagement\Resource\EmployeeResource;
use Domain\Organization\Actions\CreateEmployeeAction;
use Domain\Organization\Actions\UpdateEmployeeAction;
use Domain\Organization\DataTransferObjects\EmployeeData;
use Domain\Organization\Models\Employee;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Support\Controllers\Controller;

class EmployeeController extends Controller
{
    public function index(IndexEmployeesQuery $indexEmployeesQuery): AnonymousResourceCollection
    {
        return EmployeeResource::collection(
            $indexEmployeesQuery->paginate(pagination_per_page())
        );
    }

    public function show(int $employee): EmployeeResource
    {
        return EmployeeResource::make(Employee::query()->forAuth()->findOrFail($employee));
    }

    public function store(EmployeeStoreRequest $request, CreateEmployeeAction $createEmployeeAction): EmployeeResource
    {
        $dto = EmployeeData::from(Arr::except(
            $request->validated() +
            [
                'is_organization_manager' => false,
                'organization_id' => auth()->user()->organization_id,
            ],
            ['password_confirmation']
        ));

        return EmployeeResource::make(
            $createEmployeeAction->execute($dto)
        );
    }

    public function update(int $employee, EmployeeUpdateRequest $request, UpdateEmployeeAction $action): EmployeeResource
    {
        $employee = Employee::query()->findOrFail($employee);

        $data = EmployeeData::from(array_merge($employee->attributesToArray(), $request->validated()));

        return EmployeeResource::make(
            $action->execute($employee, $data)
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(int $employee): EmployeeResource
    {
        if ($employee == auth()->id()) {
            throw new AuthorizationException('cant delete this employee');
        }

        $employee = Employee::query()->forAuth()->findOrFail($employee);

        $employee->delete();

        return EmployeeResource::make($employee);
    }
}
