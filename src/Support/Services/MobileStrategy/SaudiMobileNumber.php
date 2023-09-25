<?php

namespace Support\Services\MobileStrategy;

class SaudiMobileNumber extends MobileNumber
{
    public function countryCode(): string
    {
        return 'SA';
    }

    public function mobileCode(): string
    {
        return '+966';
    }
}
