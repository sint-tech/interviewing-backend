<?php

namespace Domain\Skill\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class SkillData extends Data
{
    public function __construct(
        public readonly string|Optional $name,
        public readonly string|Optional $description
    ) {

    }
}
