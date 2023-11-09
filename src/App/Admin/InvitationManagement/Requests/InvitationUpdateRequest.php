<?php

namespace App\Admin\InvitationManagement\Requests;

use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Invitation\Models\Invitation;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;
use Support\Rules\ValidMobileNumberRule;
use Support\Services\MobileStrategy\MobileCountryCodeEnum;

class InvitationUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['filled', 'string', 'min:1'],
            'email' => ['filled', 'string', 'email'],
            'mobile_country_code' => ['filled',
                new Enum(MobileCountryCodeEnum::class),
            ],
            'vacancy_id' => ['filled', 'integer', Rule::exists(table_name(Vacancy::class), 'id')->withoutTrashed()],
            'interview_template_id' => ['nullable', 'integer',
                Rule::exists(table_name(InterviewTemplate::class), 'id')->withoutTrashed(),
            ],
            'mobile_number' => ['filled', 'integer',
                $this->whenFilled('mobile_number', function () {
                    (new ValidMobileNumberRule($this->enum('mobile_country_code', MobileCountryCodeEnum::class)));
                }),
            ],
            'should_be_invited_at' => ['filled', 'date', 'date_format:Y-m-d H:i', 'after:now'],
            'expired_at' => ['nullable',
                'date', 'date_format:Y-m-d H:i',
                'after:should_be_invited_at',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->invalidExpiredAt()) {
                    $validator->errors()
                        ->add('expired_at', __("expired_at should be after: {$this->invitation()->should_be_invited_at->format('Y-m-d H:i')}"));
                }
            },
        ];
    }

    public function invitation(): Invitation
    {
        return Invitation::query()->findOrFail($this->invitation);
    }

    private function invalidExpiredAt(): bool
    {
        if ($this->missing('expired_at') || $this->filled('should_be_invited_at')) {
            return false;
        }

        return $this->date('expired_at')->lessThanOrEqualTo($this->invitation()->should_be_invited_at);
    }
}
