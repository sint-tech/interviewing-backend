<?php

namespace App\Website\Auth\Factories;

use App\Website\Auth\Requests\RegisterRequest;
use Domain\Candidate\DataTransferObjects\CandidateData;

class CandidateDataFactory
{
    public static function fromRequest(RegisterRequest $request)
    {
        return CandidateData::from([
            'email' => $request->validated('email'),
            'first_name' => $request->validated('first_name'),
            'last_name' => $request->validated('last_name'),
            'full_name' => $request->validated('first_name').' '.$request->validated('last_name'),
            'mobile_number' => preg_replace('[^0-9]', '', $request->validated('mobile.number')),
            'mobile_country' => $request->validated('mobile.country'),
            'password' => $request->validated('password'),

            'current_job_title_id' => $request->validated('current_job_title_id'),
        ]);
    }
}
