<?php

namespace App\Candidate\Auth;

use App\Candidate\Auth\Requests\RegisterRequest;
use Domain\Candidate\Actions\AttachDesireJobsToCandidateAction;
use Domain\Candidate\Actions\AttachRegistrationReasonsToCandidateAction;
use Domain\Candidate\Actions\CreateCandidateAction;
use Domain\Candidate\Actions\GenerateCandidateAccessTokenAction;
use Domain\Candidate\Actions\UploadCandidateCvAction;
use Domain\Candidate\DataTransferObjects\CandidateData;
use Domain\Candidate\Models\Candidate;

class AuthService
{
    /**
     * @return array <candidate,token>
     */
    public function register(RegisterRequest $request): array
    {
        $candidateDto = CandidateData::from($request->getRegistrationData());

        $candidate = (new CreateCandidateAction($candidateDto))->execute();

        $token = $this->generateToken($candidate);

        $response = [
            $candidate,
            $token,
        ];

        if ($request->registerUsingInvitation()) {
            //todo bound candidate with the invitation
            return $response;
        }

        $commands = [
            new AttachRegistrationReasonsToCandidateAction($candidate, $request->validated('registration_reasons')),
            new AttachDesireJobsToCandidateAction($candidate, $request->validated('desire_hiring_positions')),
            new UploadCandidateCvAction($candidate, $request->file('cv'), true),
        ];

        foreach ($commands as $command) {
            $command->execute();
        }

        $response[0]->load([
            'currentJobTitle',
            'desireHiringPositions',
            'registrationReasons',
        ]);

        return $response;
    }

    public function generateToken(Candidate $candidate): string
    {
        return (new GenerateCandidateAccessTokenAction($candidate))->execute();
    }
}
