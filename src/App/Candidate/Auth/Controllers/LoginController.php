<?php

namespace App\Candidate\Auth\Controllers;

use App\Candidate\Auth\Requests\LoginRequest;
use App\Candidate\Auth\Resources\CandidateResource;
use Domain\Candidate\Actions\GenerateCandidateAccessTokenAction;
use Domain\Candidate\Models\Candidate;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Support\Controllers\Controller;

class LoginController extends Controller
{
    public function __invoke(
        LoginRequest $request
    ) {
        $candidate = Candidate::query()
            ->where('email', $request->validated('email'))
            ->firstOr(fn () => throw new AuthenticationException('email or password wrong!'));

        if (! Hash::check($request->validated('password'), $candidate->password)) {
            throw new AuthenticationException('email or password wrong!');
        }

        auth()->setUser($candidate);

        $token = (new GenerateCandidateAccessTokenAction($candidate))->execute();

        return CandidateResource::make($candidate)->additional([
            'token' => $token,
        ]);
    }
}
