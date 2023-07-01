<?php

namespace App\Candidate\Auth\Controllers;

use App\Candidate\Auth\Factories\CandidateDataFactory;
use App\Candidate\Auth\Factories\FirstCvDataFactory;
use App\Candidate\Auth\Requests\RegisterRequest;
use App\Candidate\Auth\Resources\CandidateResource;
use Domain\Candidate\Actions\AttachDesireJobsToCandidateAction;
use Domain\Candidate\Actions\AttachRegistrationReasonsToCandidateAction;
use Domain\Candidate\Actions\CreateCandidateAction;
use Domain\Candidate\Actions\GenerateCandidateAccessTokenAction;
use Domain\Candidate\Actions\UploadCandidateCvAction;
use Support\Controllers\Controller;

class RegisterController extends Controller
{
    public function __invoke(
        RegisterRequest $request
    ) {
        $candidate_data = CandidateDataFactory::fromRequest($request);

        $cv_data = FirstCvDataFactory::fromRequest($request);

        $candidate = (new CreateCandidateAction($candidate_data))->execute();

        (new AttachDesireJobsToCandidateAction($candidate, $request->validated('desire_hiring_positions')))->execute();

        (new AttachRegistrationReasonsToCandidateAction($candidate, $request->validated('registration_reasons')))->execute();

        (new UploadCandidateCvAction($candidate, $cv_data))->execute();

        $candidate->load([
            'currentJobTitle',
            'desireHiringPositions',
            'registrationReasons',
        ]);
        //todo create candidate service and add register method

        $token = (new GenerateCandidateAccessTokenAction($candidate))->execute();

        return (new CandidateResource($candidate))->additional([
            'meta' => [
                'token' => $token,
            ],
        ]);
    }
}
