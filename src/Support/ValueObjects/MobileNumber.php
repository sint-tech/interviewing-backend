<?php

namespace Support\ValueObjects;


use Propaganistas\LaravelPhone\PhoneNumber;
use Support\Services\MobileStrategy\MobileCountryCodeEnum;

class MobileNumber
{
    public function __construct(
        public readonly string $dialCode,
        public readonly int $number)
    {
        $this->validDialCode();

        $this->validMobileNumber();
    }

    public function country(): string
    {
        return match ($this->dialCode) {
            '+20' => 'Egypt',
            '+966' => 'Saudi'
        };
    }

    public function countryShortCode():string
    {
        return match ($this->dialCode) {
            '+20' => 'EG',
            '+966' => 'KSA'
        };
    }
    private function validMobileNumber(): void
    {
        $valid = (new PhoneNumber($this->number, $this->countryShortCode()))->isValid();

        if ($valid) {
            return ;
        }

        throw new \Exception(sprintf('this mobile number is invalid %s%s',$this->dialCode,$this->number));
    }

    private function validDialCode(): void
    {
        $exists = MobileCountryCodeEnum::tryFrom($this->dialCode);

        if ($exists) {
            return ;
        }

        throw new \Exception(sprintf('This dial code: %s is not supported',$this->dialCode));
    }
}
