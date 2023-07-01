<?php

namespace App\Candidate\Auth\Controllers;

use App\Candidate\Auth\Requests\SocialLoginRequest;
use App\Candidate\Auth\Resources\CandidateResource;
use Domain\Candidate\Actions\GenerateCandidateAccessTokenAction;
use Domain\Candidate\Enums\CandidateSocialAppEnum;
use Domain\Candidate\Models\Candidate;
use Illuminate\Auth\AuthenticationException;
use Support\Controllers\Controller;

class SocialLoginController extends Controller
{
    public function __invoke(
        SocialLoginRequest $request
    ) {
        $candidate = Candidate::query()
            ->whereSocialDriverName($request->enum('driver_name', CandidateSocialAppEnum::class))
            ->whereSocialDriverId($request->validated('driver_id'))
            ->firstOr(fn () => throw new AuthenticationException());

        $token = (new GenerateCandidateAccessTokenAction($candidate))->execute();

        return CandidateResource::make($candidate)
            ->additional(['token' => $token]);
    }
}
