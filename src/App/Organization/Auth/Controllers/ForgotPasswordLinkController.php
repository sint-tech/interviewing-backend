<?php
namespace App\Organization\Auth\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Support\Controllers\Controller;

class ForgotPasswordLinkController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::broker('organizations')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 500);
    }
}
