<?php

namespace Domain\QuestionManagement\DataTransferObjects;

use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class QuestionClusterDto extends Data
{
    public function __construct
    (
        public  readonly string $name,
        public Authenticatable $creator,
        public  readonly string|Optional $description,
        public readonly array|Optional $skills,
    )
    {

    }
}
