<?php

namespace App\Website\Auth\Controllers;

use Illuminate\Support\Facades\Artisan;
use Support\Controllers\Controller;

class LogoutController extends Controller
{
    public function __invoke()
    {
        $candidate = auth()->user();

        $candidate->token()->revoke();

        return response()->json([
            "message"   => "logout successfully"
        ]);
    }
}
