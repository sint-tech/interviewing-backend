<?php

namespace Domain\Organization\Actions;

use Domain\Organization\Models\Employee;

class GenerateEmployeeAccessTokenAction
{
    public const TOKEN_NAME = 'Laravel Password Grant Client FOR ORGANIZATION EMPLOYEE';

    public function execute(Employee $employee): string
    {
        return $employee->createToken(self::TOKEN_NAME)->plainTextToken;
    }
}
