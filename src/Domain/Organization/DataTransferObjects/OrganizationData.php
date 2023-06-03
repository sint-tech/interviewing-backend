<?php

namespace Domain\Organization\DataTransferObjects;

use Spatie\LaravelData\Data;

class OrganizationData extends Data
{
    public function __construct
    (
        public readonly string $name,
    )
    {

    }
}
