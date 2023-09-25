<?php

namespace Support\ValueObjects;


use Carbon\Carbon;

class DateToHumanReadValueObject
{

    public function __construct(
        protected readonly Carbon|null $date
    )
    {
    }

    public function toFullDateTimeFormat(): string
    {
        if (! $this->date) {
            return '';
        }

        return $this->date->format('Y-m-d H:m');
    }

    public function __toString(): string
    {
        return $this->toFullDateTimeFormat();
    }
}
