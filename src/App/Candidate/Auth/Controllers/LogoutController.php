<?php

namespace App\Candidate\Auth\Controllers;

use Support\Controllers\Controller;

class LogoutController extends Controller
{
    public function __invoke()
    {
        $candidate = auth()->user();

        $candidate->currentAccessToken()->delete();

        return response()->json([
            'message' => 'logout successfully',
        ]);
    }
}
