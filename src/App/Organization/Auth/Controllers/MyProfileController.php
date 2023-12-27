<?php

namespace App\Organization\Auth\Controllers;

use App\Organization\Auth\Resources\AuthEmployeeResource;
use Support\Controllers\Controller;

class MyProfileController extends Controller
{
    public function __invoke(): AuthEmployeeResource
    {
        return AuthEmployeeResource::make(
            auth()->user()->load('organization')
        );
    }
}
