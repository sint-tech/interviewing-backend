<?php

namespace Domain\Invitation\DataTransferObjects;

use Domain\Invitation\ValueObjects\InvitationBatch;
use Spatie\LaravelData\Data;
use Support\Services\MobileStrategy\MobileCountryCodeEnum;
use Support\Services\MobileStrategy\MobileCountryEnum;
use Support\Services\MobileStrategy\MobileNumberFactory;

class InvitationDto extends Data
{
    public function __construct(
        public readonly string $email,
        public readonly MobileCountryCodeEnum $mobile_country_code,
        public readonly int $dirty_mobile_number,
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
}
