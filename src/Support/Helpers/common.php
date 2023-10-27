<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Support\Services\MobileStrategy\MobileCountryCodeEnum;
use Support\Services\MobileStrategy\MobileCountryEnum;

if (! function_exists('supported_countries_codes')) {

    function supported_countries_codes(): array
    {
        return enum_values_to_array(MobileCountryCodeEnum::class);
    }
}

if (! function_exists('supported_countries')) {

    function supported_countries(): array
    {
        return enum_values_to_array(MobileCountryEnum::class);
    }
}

if (! function_exists('enum_values_to_array')) {
    function enum_values_to_array(string $enumClass)
    {
        $enums = enum_to_array($enumClass);

        return array_map(fn ($item) => $item->value, $enums);
    }
}

if (! function_exists('enum_to_array')) {

    function enum_to_array(string $enumClass): array
    {

        $reflection = new ReflectionClass($enumClass);

        return array_values($reflection->getConstants());
    }
}

if (! function_exists('table_name')) {
    /**
     * get model db table name
     */
    function table_name(string|Model|Builder $object): string
    {
        if ($object instanceof Model) {
            return $object->getTable();
        } elseif ($object instanceof Builder) {
            return $object->getModel()->getTable();
        }

        return (new $object)->getTable();
    }
}

if (! function_exists('pagination_per_page')) {

    function pagination_per_page(int $per_page = 25, string $inputName = 'per_page'): int
    {
        return request()->integer($inputName, $per_page);
    }
}
