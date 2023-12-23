<?php

namespace Support\Services\MobileStrategy;

class SaudiMobileNumber extends MobileNumber
{
    public function countryCode(): string
    {
        return 'SA';
    }

    public function dialCode(): string
    {
        return '+966';
    }
}
