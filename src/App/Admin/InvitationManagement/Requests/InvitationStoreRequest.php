<?php

namespace App\Admin\InvitationManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Propaganistas\LaravelPhone\Rules\Phone;
use Support\Rules\ValidMobileNumberRule;
use Support\Services\MobileStrategy\MobileCountryCodeEnum;
use Support\Services\MobileStrategy\MobileCountryEnum;
use Support\Services\MobileStrategy\MobileNumberFactory;

class InvitationStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'  => ['required','string','min:1'],
            'email' => ['required','string','email'],
            'mobile_country_code'    => ['required',
                new Enum(MobileCountryCodeEnum::class),
            ],
            'interview_template_id' => ['required','integer',Rule::exists('interview_templates','id')->withoutTrashed()],
            'mobile_number' => ['required','integer',
                (new ValidMobileNumberRule($this->enum('mobile_country_code',MobileCountryCodeEnum::class)))
            ],
            'expired_at' => ['nullable','date','date_format:Y-m-d H:i','after:now'],
        ];
    }
}
