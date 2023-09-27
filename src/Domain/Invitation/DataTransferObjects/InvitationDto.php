<?php

namespace Domain\Invitation\DataTransferObjects;

use Carbon\CarbonImmutable;
use Domain\Invitation\ValueObjects\InvitationBatch;
use Illuminate\Support\Optional;
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
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i')]
        public readonly \DateTime|null|Optional $expired_at,
    )
    {
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
            'name'  => ['required','string','min:1'],
            'email' => ['required','email'],
            'mobile_country_code' => ['required',new Enum(MobileCountryCodeEnum::class)],
            'dirty_mobile_number'   => ['required', 'integer',
                new ValidMobileNumberRule(MobileCountryCodeEnum::from($context->fullPayload['mobile_country_code']))
            ],
            'expired_at'    => ['nullable','date','date_format:Y-m-d H:i','after:now']
        ];
    }
}
