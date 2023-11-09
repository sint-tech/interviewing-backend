<?php

namespace App\Admin\InvitationManagement\Requests;

use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;
use Support\Rules\ValidMobileNumberRule;
use Support\Services\MobileStrategy\MobileCountryCodeEnum;

class InvitationStoreRequest extends FormRequest
{
    private string $failed_interview_template_id_message = 'the :attribute should be belongs to selected vacancy';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:1'],
            'email' => ['required', 'string', 'email'],
            'mobile_country_code' => ['required',
                new Enum(MobileCountryCodeEnum::class),
            ],
            'vacancy_id' => ['required', 'integer', Rule::exists(table_name(Vacancy::class), 'id')
                ->whereNotNull('interview_template_id')
                ->withoutTrashed(),
            ],
            'interview_template_id' => ['nullable', 'integer', Rule::exists(table_name(InterviewTemplate::class), 'id')->withoutTrashed()],
            'mobile_number' => ['required', 'integer',
                (new ValidMobileNumberRule($this->enum('mobile_country_code', MobileCountryCodeEnum::class))),
            ],
            'should_be_invited_at' => ['required', 'date', 'date_format:Y-m-d H:i', 'after:now'],
            'expired_at' => ['nullable', 'date', 'date_format:Y-m-d H:i', 'after:should_be_invited_at'],
        ];
    }

    public function after()
    {
        return [
            function (Validator $validator) {
                if ($this->invalidInterviewTemplate()) {
                    $validator->errors()->add('interview_template_id', __('the interview_template_id should belongs to selected vacancy'));
                }
            },
        ];
    }

    private function invalidInterviewTemplate(): bool
    {
        if ($this->missing('interview_template_id')) {
            return false;
        }

        return Vacancy::query()
            ->find($this->validated('vacancy_id'))
            ->defaultInterviewTemplate()
            ->whereKey($this->validated('interview_template_id'))
            ->doesntExist();
    }

    public function messages()
    {
        return [
            'vacancy_id.exists' => __('the vacancy_id is not valid, ensure it exists and has default interview template'),
        ];
    }
}
