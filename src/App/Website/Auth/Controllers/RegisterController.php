<?php

namespace App\Website\Auth\Controllers;

use App\Website\Auth\Factories\CandidateDataFactory;
use App\Website\Auth\Requests\RegisterRequest;
use App\Website\Auth\Resources\CandidateResource;
use Domain\Candidate\Actions\CreateCandidateAction;
use Domain\Candidate\Actions\GenerateCandidateAccessTokenAction;
use Laravel\Passport\Client;
use Support\Controllers\Controller;

class RegisterController extends Controller
{
    public function __invoke
    (
        RegisterRequest $request
    )
    {
        $data = CandidateDataFactory::fromRequest($request);

        $candidate = (new CreateCandidateAction($data))->execute();

        $token = (new GenerateCandidateAccessTokenAction($candidate))->execute();

        return (new CandidateResource($candidate))->additional([
            'meta' => [
                "token" => $token
            ]
        ]);
    }
}
