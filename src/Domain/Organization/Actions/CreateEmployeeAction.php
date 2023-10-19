<?php

namespace Domain\Organization\Actions;

use Domain\Organization\DataTransferObjects\EmployeeData;
use Domain\Organization\Models\Employee;

class CreateEmployeeAction
{
    public function execute(EmployeeData $employeeData): Employee
    {
        return Employee::query()->create($employeeData->toArray());
    }
}
