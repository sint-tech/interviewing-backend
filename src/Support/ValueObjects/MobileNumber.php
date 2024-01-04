<?php

namespace Support\ValueObjects;

use Propaganistas\LaravelPhone\PhoneNumber;
use Support\Services\MobileStrategy\MobileCountryCodeEnum;

class MobileNumber
{
    public readonly string $country;

    public readonly string $countryShortCode;

    public function __construct(
        public readonly string $dialCode,
        public readonly int $number
    ) {
        $this->validDialCode();

        $this->setCountry();

        $this->setCountryShortCode();

        $this->validMobileNumber();
    }

    private function setCountry(): void
    {
        $this->country = match ($this->dialCode) {
            '+20' => 'Egypt',
            '+966' => 'Saudi'
        };
    }

    protected function setCountryShortCode(): void
    {
        $this->countryShortCode = match ($this->dialCode) {
            '+20' => 'EG',
            '+966' => 'KSA'
        };
    }

    private function validMobileNumber(): void
    {
        $valid = (new PhoneNumber($this->number, $this->countryShortCode))->isValid();

        if ($valid) {
            return;
        }

        throw new \Exception(sprintf('this mobile number is invalid %s%s', $this->dialCode, $this->number));
    }

    private function validDialCode(): void
    {
        $exists = MobileCountryCodeEnum::tryFrom($this->dialCode);

        if ($exists) {
            return;
        }

        throw new \Exception(sprintf('This dial code: %s is not supported', $this->dialCode));
    }

    public static function validateMobileNumber(string|MobileCountryCodeEnum $mobileDial, string|int $mobileNumber): bool
    {
        try {
            if ($mobileDial instanceof MobileCountryCodeEnum) {
                $mobileDial = $mobileDial->value;
            }

            new self($mobileDial, $mobileNumber);

            return true;
        } catch (\Exception $exception) {
            dd($exception);

            return false;
        }
    }

    public function __toString(): string
    {
        return $this->dialCode.$this->number;
    }
}
