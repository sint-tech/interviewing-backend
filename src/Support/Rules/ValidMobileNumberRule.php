<?php

namespace Support\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Support\Services\MobileStrategy\MobileCountryCodeEnum;
use Support\Services\MobileStrategy\MobileCountryEnum;
use Support\ValueObjects\MobileNumber;

class ValidMobileNumberRule implements ValidationRule
{
    public function __construct(
        protected MobileCountryCodeEnum|MobileCountryEnum|null $mobileCountryEnum = null
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_null($this->mobileCountryEnum)) {
            $fail('mobile country code must be filled');
        }
        if ($this->mobileNotValidWithThisCountry($value)) {
            $fail("the :attribute value not valid with country : {$this->mobileCountryEnum->value}");
        }
    }

    private function mobileNotValidWithThisCountry(mixed $value): bool
    {
        return ! MobileNumber::validateMobileNumber($this->mobileCountryEnum, $value);
    }
}
