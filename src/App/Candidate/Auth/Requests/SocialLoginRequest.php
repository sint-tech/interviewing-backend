<?php

namespace App\Candidate\Auth\Requests;

use Domain\Candidate\Enums\CandidateSocialAppEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SocialLoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'driver_name' => ['required', 'string', Rule::enum(CandidateSocialAppEnum::class)],
            'driver_id' => ['required', 'string'],
        ];
    }

    protected function supportedSocialDrivers(): array
    {
        return [
            CandidateSocialAppEnum::Google->value,
            CandidateSocialAppEnum::Linkedin->value,
        ];
    }
}
