<?php

namespace App\Organization\Auth\Controllers;

use App\Organization\Auth\Requests\LoginRequest;
use App\Organization\Auth\Resources\AuthEmployeeResource;
use Domain\Organization\Actions\AuthenticateEmployeeWhenExistAction;
use Domain\Organization\Actions\GenerateEmployeeAccessTokenAction;
use Illuminate\Http\Request;
use Support\Controllers\Controller;

class LoginController extends Controller
{
    public function __invoke(
        LoginRequest $request,
        AuthenticateEmployeeWhenExistAction $authenticateEmployeeWhenExistAction,
        GenerateEmployeeAccessTokenAction $generateEmployeeAccessTokenAction
    )
    {
        $employee = $authenticateEmployeeWhenExistAction->execute($request->validated('email'),$request->validated('password'));

        return AuthEmployeeResource::make($employee)->additional([
            'token' => $generateEmployeeAccessTokenAction->execute($employee)
        ]);
    }
}
