<?php

namespace App\Organization\Auth\Controllers;

use Support\Controllers\Controller;
use Domain\Organization\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Organization\Auth\Requests\ResetPasswordRequest;

class ResetPasswordController extends Controller
{
    public function __invoke(ResetPasswordRequest $request)
    {

        $status = Password::broker('organizations')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Employee $employee, string $password) {
                $employee->forceFill([
                    'password' => Hash::make($password)
                ]);

                $employee->save();
                event(new PasswordReset($employee));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 400);
    }
}
