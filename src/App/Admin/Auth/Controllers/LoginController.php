<?php

namespace App\Admin\Auth\Controllers;

use App\Admin\Auth\Resources\AdminResource;
use Domain\Users\Actions\GenerateAdminAccessTokenAction;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Support\Controllers\Controller;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $credential_data = ['email', 'password'];

        if (! auth()->attempt($request->only($credential_data))) {
            throw new AuthenticationException('wrong email or password');
        }

        $accessToken = (new GenerateAdminAccessTokenAction(auth()->user()))->execute();

        return AdminResource::make(auth()->user())->additional([
            'meta' => [
                'token' => $accessToken,
            ],
        ]);
    }
}
