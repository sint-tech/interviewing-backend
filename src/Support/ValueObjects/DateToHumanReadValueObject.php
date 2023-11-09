<?php

namespace Support\ValueObjects;

use Carbon\Carbon;

class DateToHumanReadValueObject
{
    public function __construct(
        protected readonly ?Carbon $date
    ) {
    }

    public static function format(?Carbon $date): ?string
    {
        return (new self($date))->toFullDateTimeFormat();
    }

    public function toFullDateTimeFormat(): ?string
    {
        return $this->date?->format('Y-m-d H:i');
    }

    public function __toString(): string
    {
        return $this->toFullDateTimeFormat();
    }
}
