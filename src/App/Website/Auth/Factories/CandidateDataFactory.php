<?php

namespace App\Website\Auth\Factories;

use App\Website\Auth\Requests\RegisterRequest;
use Domain\Candidate\DataTransferObjects\CandidateData;
use Illuminate\Http\Request;

class CandidateDataFactory
{
    public static function fromRequest(RegisterRequest $request)
    {
        return CandidateData::from([
            "first_name"    => $request->validated("first_name"),
            "last_name"     => $request->validated("last_name"),
            "email"         => $request->validated("email"),
            "mobile_number" => $request->validated("mobile_number"),
            "mobile_country_code" => $request->validated("mobile_country_code"),
            "full_name"    => $request->validated("first_name") . " " . $request->validated("last_name"),
            "password"     => $request->validated("password"),
        ]);
    }
}
