<?php

namespace Support\Services\MobileStrategy;

enum MobileCountryCodeEnum: string
{
    case Egypt = '+20';

    case Saudi = '+966';

    public static function getValues(): array
    {
        return [
            self::Egypt->value,
            self::Saudi->value,
        ];
    }
}
