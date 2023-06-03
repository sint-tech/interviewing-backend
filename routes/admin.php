<?php

use App\Admin\Organization\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::apiResource('organizations', OrganizationController::class);
