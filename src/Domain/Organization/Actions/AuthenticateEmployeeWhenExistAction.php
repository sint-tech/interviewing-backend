<?php

namespace Domain\Organization\Actions;

use Domain\Organization\Models\Employee;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class AuthenticateEmployeeWhenExistAction
{
    /**
     * @throws AuthenticationException
     */
    public function execute(string $email, string $password): Employee
    {

        $employee = Employee::query()
            ->where('email', $email)
            ->firstOr(fn () => throw new AuthenticationException('email or password wrong!'));

        if (! Hash::check($password, $employee->password)) {
            throw new AuthenticationException('email or password wrong!');
        }

        return $employee->load('organization');
    }
}
