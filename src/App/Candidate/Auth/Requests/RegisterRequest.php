<?php

namespace App\Candidate\Auth\Requests;

use Domain\Candidate\Enums\RegistrationReasonsAvailabilityStatusEnum;
use Domain\Candidate\Models\Candidate;
use Domain\Candidate\Models\RegistrationReason;
use Domain\Invitation\Models\Invitation;
use Domain\JobTitle\Enums\AvailabilityStatusEnum;
use Domain\JobTitle\Models\JobTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Password;
use Propaganistas\LaravelPhone\Rules\Phone;
use Support\Services\MobileStrategy\MobileCountryCodeEnum;
use Support\ValueObjects\MobileNumber;

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
            'mobile' => [Rule::requiredIf(! $this->registerUsingInvitation()), 'array:dial_code,number'],
            'mobile.dial_code' => ['required_with:mobile', Rule::enum(MobileCountryCodeEnum::class)],
            'mobile.number' => [
                'required_with:mobile', 'integer', (new Phone())->country(['EG']),
                Rule::unique(Candidate::class, 'mobile_number')->where('mobile_dial_code', $this->input('mobile.mobile_dial_code')),
            ],
            'password' => ['required', Password::min(8)->letters()->numbers()],

            'current_job_title_id' => [Rule::requiredIf(! $this->registerUsingInvitation()),
                Rule::exists(JobTitle::class, 'id')
                    ->whereNull('deleted_at')
                    ->where('availability_status', AvailabilityStatusEnum::Active->value),
            ],
            'desire_hiring_positions' => [Rule::requiredIf(! $this->registerUsingInvitation()), 'array', 'min:1', 'max:100', 'distinct'],
            'desire_hiring_positions.*' => ['integer', Rule::exists(JobTitle::class, 'id')
                ->whereNull('deleted_at')
                ->where('availability_status', AvailabilityStatusEnum::Active->value),
            ],
            'registration_reasons' => [Rule::requiredIf(! $this->registerUsingInvitation()), 'array', 'min:1', 'max:100', 'distinct'],
            'registration_reasons.*' => ['integer', Rule::exists(RegistrationReason::class, 'id')
                ->whereNull('deleted_at')
                ->where('availability_status', RegistrationReasonsAvailabilityStatusEnum::Active->value),
            ],
            'cv' => [Rule::requiredIf(! $this->registerUsingInvitation()), File::types('pdf')->max(2000000)],
        ];
    }

    public function mobileNumber(): ?MobileNumber
    {
        if($this->isNotFilled('mobile')) {
            return null;
        }

        return new MobileNumber($this->validated('mobile.dial_code'), $this->validated('mobile.number'));
    }

    public function registerUsingInvitation(): bool
    {
        return $this->route()->hasParameter('invitation');
    }

    public function getRegistrationData(): array
    {
        if (! $this->registerUsingInvitation()) {
            return $this->safe()->except(['mobile']) + ['mobile_number' =>$this->mobileNumber()]
                + ['full_name' => $this->validated('first_name') . ' ' . $this->validated('last_name')];
        }

        return $this->safe([
            'first_name',
            'last_name',
            'email',
            'password'
        ]);
    }

    public function invitation(): Invitation
    {
        return Invitation::query()->where('email',$this->validated('email'))->whereKey($this->route('invitation'))->firstOrFail();
    }
}
