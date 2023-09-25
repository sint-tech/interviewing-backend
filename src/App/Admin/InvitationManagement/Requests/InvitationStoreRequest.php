<?php

namespace App\Admin\InvitationManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Propaganistas\LaravelPhone\Rules\Phone;
use Support\Services\MobileStrategy\MobileCountryCodeEnum;
use Support\Services\MobileStrategy\MobileCountryEnum;
use Support\Services\MobileStrategy\MobileNumberFactory;

class InvitationStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required','string','email'],
            'mobile_country_code'    => ['required',
                new Enum(MobileCountryCodeEnum::class),
            ],
            'mobile_number' => ['required','integer',
                (new MobileNumberFactory())
                    ->createMobileNumberInstance(
                        $this->enum('mobile_country_code',MobileCountryCodeEnum::class)
                    )->mobileValidationRule()
            ],
            'expired_at' => ['nullable','date','date_format:Y-m-d H:i','after:now'],
        ];
    }
}
