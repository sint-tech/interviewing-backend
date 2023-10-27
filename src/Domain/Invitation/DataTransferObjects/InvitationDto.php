<?php

namespace Domain\Invitation\DataTransferObjects;

use Domain\Invitation\ValueObjects\InvitationBatch;
use Illuminate\Support\Optional;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Support\Rules\ValidMobileNumberRule;
use Support\Services\MobileStrategy\MobileCountryCodeEnum;
use Support\Services\MobileStrategy\MobileNumberFactory;

class InvitationDto extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly MobileCountryCodeEnum $mobile_country_code,
        public readonly int $dirty_mobile_number,
        public int $interview_template_id,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i')]
        public readonly \DateTime $should_be_invited_at,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i')]
        public readonly \DateTime|null|Optional $expired_at,
    ) {
        $mobileStrategy = (new MobileNumberFactory())
            ->createMobileNumberInstance($this->mobile_country_code);

        $this->additional([
            'batch' => InvitationBatch::getInstance()->getNextBatch(),
            'mobile_number' => $mobileStrategy->trimToNationalInteger($this->dirty_mobile_number),
        ]);

        $this->except('dirty_mobile_number');
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['required', 'string', 'min:1'],
            'email' => ['required', 'email'],
            'mobile_country_code' => ['required', new Enum(MobileCountryCodeEnum::class)],
            'dirty_mobile_number' => ['required', 'integer',
                new ValidMobileNumberRule(MobileCountryCodeEnum::from($context->fullPayload['mobile_country_code'])),
            ],
            'interview_template_id' => ['required', 'integer', Rule::exists('interview_templates', 'id')->withoutTrashed()],
            'expired_at' => ['nullable', 'date', 'date_format:Y-m-d H:i', 'after:should_be_invited_at'],
            'should_be_invited_at' => ['required', 'date_format:Y-m-d H:i', 'date', 'after:now'],
        ];
    }
}
