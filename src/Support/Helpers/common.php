<?php

use Support\Services\MobileStrategy\{
    MobileCountryCodeEnum,
    MobileCountryEnum
};

if (! function_exists('supported_countries_codes')) {

    /**
     * @return array
     */
    function supported_countries_codes(): array
    {
        return enum_values_to_array(MobileCountryCodeEnum::class);
    }
}

if (! function_exists('supported_countries')) {

    /**
     * @return array
     */
    function supported_countries(): array
    {
        return enum_values_to_array(MobileCountryEnum::class);
    }
}

if(! function_exists('enum_values_to_array')) {
    function enum_values_to_array(string $enumClass) {
        $enums = enum_to_array($enumClass);

        return array_map(fn($item) => $item->value,$enums);
    }
}

if(! function_exists('enum_to_array')) {

    function enum_to_array(string $enumClass): array
    {

        $reflection = new ReflectionClass($enumClass);

        return array_values($reflection->getConstants());
    }
}
