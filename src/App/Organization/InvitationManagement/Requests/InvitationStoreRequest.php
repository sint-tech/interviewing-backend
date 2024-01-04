<?php

namespace App\Organization\InvitationManagement\Requests;

use Domain\Vacancy\Models\Vacancy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Support\Rules\ValidMobileNumberRule;
use Support\Services\MobileStrategy\MobileCountryCodeEnum;

class InvitationStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:1'],
            'email' => ['required', 'string', 'email'],
            'mobile_country_code' => ['required',
                new Enum(MobileCountryCodeEnum::class),
            ],
            'mobile_number' => ['required', 'integer',
                (new ValidMobileNumberRule($this->enum('mobile_country_code', MobileCountryCodeEnum::class))),
            ],
            'vacancy_id' => ['required', 'integer', Rule::exists(table_name(Vacancy::class), 'id')
                ->whereNotNull('interview_template_id')
                ->where('organization_id', auth()->user()->organization_id)
                ->withoutTrashed(),
            ],
            'should_be_invited_at' => ['required', 'date', 'date_format:Y-m-d H:i', 'after:now'],
            'expired_at' => ['nullable', 'date', 'date_format:Y-m-d H:i', 'after:should_be_invited_at'],
        ];
    }
}
