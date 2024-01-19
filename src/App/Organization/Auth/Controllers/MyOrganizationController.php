<?php

namespace App\Organization\Auth\Controllers;

use App\Organization\Auth\Resources\OrganizationResource;
use Support\Controllers\Controller;

class MyOrganizationController extends Controller
{
    public function __invoke(): OrganizationResource
    {
        return OrganizationResource::make(auth()->user()->organization);
    }
}
