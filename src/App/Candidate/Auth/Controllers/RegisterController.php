<?php

namespace App\Candidate\Auth\Controllers;

use App\Candidate\Auth\AuthService;
use App\Candidate\Auth\Requests\RegisterRequest;
use App\Candidate\Auth\Resources\CandidateResource;
use Support\Controllers\Controller;

class RegisterController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
    }

    public function __invoke(
        RegisterRequest $request,
        int $invitation = null,
    ) {
        [$candidate, $token] = $this->authService->register($request);

        return CandidateResource::make(
            $candidate
        )->additional(['token' => $token]);
    }
}
