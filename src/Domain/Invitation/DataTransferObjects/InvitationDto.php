<?php

namespace Domain\Invitation\DataTransferObjects;

use Domain\Invitation\Models\Invitation;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Support\Optional;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithCastable;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Support\Data\Creator;
use Support\Rules\ValidMobileNumberRule;
use Support\Services\MobileStrategy\MobileCountryCodeEnum;
use Support\Services\MobileStrategy\MobileNumberFactory;
use Illuminate\Validation\Validator;

class InvitationDto extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly MobileCountryCodeEnum $mobile_country_code,
        public readonly int $dirty_mobile_number,
        public readonly int $vacancy_id,
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d H:i', 'Y-m-d\TH:i:s.u\Z'])]
        public readonly \DateTime $should_be_invited_at,
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d H:i', 'Y-m-d\TH:i:s.u\Z'])]
        public readonly \DateTime|null|Optional $expired_at,
        #[WithCastable(Creator::class, lazy_load_instance: false)]
        public readonly ?Creator $creator,
    ) {
        $mobileStrategy = (new MobileNumberFactory())
            ->createMobileNumberInstance($this->mobile_country_code);

        $this->additional([
            'mobile_number' => $mobileStrategy->trimToNationalInteger($this->dirty_mobile_number),
        ]);

        $this->additional([
            'creator_id' => $this->creator->creator_id,
            'creator_type' => $this->creator->creator_type,
        ]);

        $this->except('dirty_mobile_number');
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['required', 'string', 'min:1'],
            'email' => ['required', 'email'],
            'mobile_country_code' => ['required', Rule::enum(MobileCountryCodeEnum::class)],
            'vacancy_id' => [
                'required', 'integer', Rule::exists(Vacancy::class, 'id'),
                function (string $attribute, mixed $value, \Closure $fail) use ($context) {
                    if (Invitation::query()->where('email', $context->fullPayload['email'])->where('vacancy_id', $value)->exists()) {
                        $fail(__('Invitation for ' . $context->fullPayload['email'] . ' had been created/sent before'));
                    }
                }
            ],
            'expired_at' => ['nullable', 'date', 'date_format:Y-m-d H:i', 'after:should_be_invited_at'],
            'should_be_invited_at' => ['required', 'date_format:Y-m-d H:i', 'date', 'after:now'],
        ];
    }

    public static function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->has('mobile_country_code')) {
                return;
            }

            $validator->addRules([
                'dirty_mobile_number' => [
                    'required', 'integer',
                    new ValidMobileNumberRule(MobileCountryCodeEnum::from($validator->getData()['mobile_country_code'])),
                ],
            ]);
        });
    }

    public static function messages(...$args): array
    {
        return [
            'mobile_country_code.Illuminate\Validation\Rules\Enum' => 'This country code :input is not supported please choose from ' . implode(', ', MobileCountryCodeEnum::getValues()),
        ];
    }
}
