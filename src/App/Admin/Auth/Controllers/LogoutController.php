<?php

namespace App\Admin\Auth\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Support\Controllers\Controller;

class LogoutController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'logout successfully',
        ]);
    }
}
