<?php

namespace App\Organization\EmployeeManagement\Controllers;

use App\Organization\EmployeeManagement\Queries\IndexEmployeesQuery;
use App\Organization\EmployeeManagement\Requests\EmployeeStoreRequest;
use App\Organization\EmployeeManagement\Resource\EmployeeResource;
use Domain\Organization\Actions\CreateEmployeeAction;
use Domain\Organization\DataTransferObjects\EmployeeData;
use Domain\Organization\Models\Employee;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;
use Support\Controllers\Controller;

class EmployeeController extends Controller
{
    public function index(IndexEmployeesQuery $indexEmployeesQuery)
    {
        return EmployeeResource::collection(
            $indexEmployeesQuery->paginate(pagination_per_page())
        );
    }

    public function show(int $employee)
    {
        return EmployeeResource::make(Employee::query()->forAuth()->findOrFail($employee));
    }

    public function store(EmployeeStoreRequest $request, CreateEmployeeAction $createEmployeeAction)
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

    public function update()
    {
        //
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
