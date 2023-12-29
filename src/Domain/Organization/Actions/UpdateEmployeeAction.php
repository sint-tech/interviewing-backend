<?php

namespace Domain\Organization\Actions;

use Domain\Organization\DataTransferObjects\EmployeeData;
use Domain\Organization\Models\Employee;

class UpdateEmployeeAction
{
    public function execute(Employee $employee, EmployeeData $data): Employee
    {
        $employee->update($data->except('organization_id')->toArray());

        return $employee->refresh();
    }
}
