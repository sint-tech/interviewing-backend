<?php

namespace App\Website\Auth\Factories;

use App\Website\Auth\Requests\RegisterRequest;
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
