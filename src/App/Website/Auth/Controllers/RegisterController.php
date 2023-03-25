<?php

namespace App\Website\Auth\Controllers;

use App\Website\Auth\Factories\CandidateDataFactory;
use App\Website\Auth\Requests\RegisterRequest;
use App\Website\Auth\Resources\CandidateResource;
use Domain\Candidate\Actions\AttachDesireJobsToCandidateAction;
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

        (new AttachDesireJobsToCandidateAction($candidate,$request->validated("desire_hiring_positions")))->execute();

        $candidate->load([
            "currentJobTitle",
            "desireHiringPositions"
        ]);
        //todo create candidate service and add register method

        $token = (new GenerateCandidateAccessTokenAction($candidate))->execute();

        return (new CandidateResource($candidate))->additional([
            'meta' => [
                "token" => $token
            ]
        ]);
    }
}
