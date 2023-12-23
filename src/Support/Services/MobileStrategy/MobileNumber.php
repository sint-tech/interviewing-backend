<?php

namespace Support\Services\MobileStrategy;

use Propaganistas\LaravelPhone\PhoneNumber;
use Propaganistas\LaravelPhone\Rules\Phone;

abstract class MobileNumber
{
    /**
     * get the country code refer to ISO 3166
     * https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2
     */
    abstract public function countryCode(): string;

    /**
     * get the country mobile code
     * https://en.wikipedia.org/wiki/List_of_country_calling_codes
     */
    abstract public function dialCode(): string;

    public function mobileValidationRule(): Phone
    {
        return (new Phone())->mobile()->country($this->countryCode());
    }

    public function validMobileNumber(int $mobile_number): bool
    {
        return (new PhoneNumber($mobile_number, $this->countryCode()))->isValid();
    }

    public function formatNational(string $mobile_number): string
    {
        return (new PhoneNumber($mobile_number, $this->countryCode()))->formatNational();
    }

    public function trimToNationalInteger(string $mobile_number): int
    {
        return (int) preg_replace('/[^0-9]/', '', $this->formatNational($mobile_number));
    }
}
