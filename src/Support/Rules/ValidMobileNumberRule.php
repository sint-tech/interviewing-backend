<?php

namespace Support\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Support\Services\MobileStrategy\MobileCountryCodeEnum;
use Support\Services\MobileStrategy\MobileCountryEnum;
use Support\Services\MobileStrategy\MobileNumberFactory;

class ValidMobileNumberRule implements ValidationRule
{
    public function __construct(
        protected MobileCountryCodeEnum|MobileCountryEnum $mobileCountryEnum
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->mobileNotValidWithThisCountry($value)) {
            $fail("the :attribute value not valid with country : {$this->mobileCountryEnum->value}");
        }
    }

    private function mobileNotValidWithThisCountry(mixed $value): bool
    {
        return ! (new MobileNumberFactory())->createMobileNumberInstance(
            $this->mobileCountryEnum
        )->validMobileNumber($value);
    }
}
