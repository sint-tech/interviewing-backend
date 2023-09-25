<?php

namespace Support\Services\MobileStrategy;

class MobileNumberFactory
{
    public function createMobileNumberInstance(MobileCountryEnum|MobileCountryCodeEnum $countryEnum): MobileNumber
    {
        return match ($countryEnum) {
            $countryEnum::Egypt => (new EgyptMobileNumber()),
            $countryEnum::Saudi => (new SaudiMobileNumber())
        };
    }
}
