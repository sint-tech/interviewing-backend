<?php

namespace Support\Services\MobileStrategy;

class EgyptMobileNumber extends MobileNumber
{
    public function countryCode(): string
    {
        return 'EG';
    }

    public function mobileCode(): string
    {
        return '+20';
    }
}
