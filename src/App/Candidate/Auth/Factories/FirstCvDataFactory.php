<?php

namespace App\Candidate\Auth\Factories;

use App\Candidate\Auth\Requests\RegisterRequest;
use Domain\Candidate\DataTransferObjects\CvData;

class FirstCvDataFactory
{
    public static function fromRequest(RegisterRequest $request)
    {
        return CvData::from([
            'cv' => $request->file('cv'),
            'used_when_registered' => true,
        ]);
    }
}
