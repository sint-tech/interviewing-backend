<?php

namespace App\Website\Auth\Requests;

use Domain\Candidate\Enums\RegistrationReasonsAvailabilityStatusEnum;
use Domain\Candidate\Models\Candidate;
use Domain\Candidate\Models\RegistrationReason;
use Domain\JobTitle\Enums\AvailabilityStatusEnum;
use Domain\JobTitle\Models\JobTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Password;
use Propaganistas\LaravelPhone\Rules\Phone;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->guest();
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:2', 'max:255'],
            'last_name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', Rule::unique(Candidate::class, 'email')],
            'mobile' => ['required', 'array:country,number'],
            'mobile.country' => ['required', Rule::in(['EG'])],
            'mobile.number' => [
                'required', 'integer', (new Phone())->country(['EG']),
                Rule::unique(Candidate::class, 'mobile_number')->where('mobile_country', $this->input('mobile.country')),
            ],
            'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],

            'current_job_title_id' => ['required',
                Rule::exists(JobTitle::class, 'id')
                    ->whereNull('deleted_at')
                    ->where('availability_status', AvailabilityStatusEnum::Active->value),
            ],
            'desire_hiring_positions' => ['required', 'array', 'min:1', 'max:100', 'distinct'],
            'desire_hiring_positions.*' => ['integer', Rule::exists(JobTitle::class, 'id')
                ->whereNull('deleted_at')
                ->where('availability_status', AvailabilityStatusEnum::Active->value),
            ],
            'registration_reasons' => ['required', 'array', 'min:1', 'max:100', 'distinct'],
            'registration_reasons.*' => ['integer', Rule::exists(RegistrationReason::class, 'id')
                ->whereNull('deleted_at')
                ->where('availability_status', RegistrationReasonsAvailabilityStatusEnum::Active->value),
            ],
            'cv' => ['required', File::types('pdf')],
        ];
    }
}
