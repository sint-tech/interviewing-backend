<?php

namespace Domain\ReportManagement\DataTransferObjects;

use Spatie\LaravelData\Data;

class ReportValueDto extends Data
{
    public function __construct
    (
        public readonly string $key,
        public readonly mixed $value,
//        public readonly string $type = 'null' | 'string' | 'int' | 'float' | 'double' | 'array' | 'bool' | 'object' | 'json' | 'array'
    )
    {}
}
