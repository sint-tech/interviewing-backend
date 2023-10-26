<?php

namespace Domain\Vacancy\DataTransferObjects;

use Illuminate\Support\Optional;
use Illuminate\Validation\Rules\Exists;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Support\Traits\DTO\HasCreator;

class VacancyDto extends Data
{
    use HasCreator;

    public function __construct(
        public readonly string $title,
        public readonly string|Optional|null $description = null,

        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i')]
        public readonly ?\DateTime $started_at,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i')]
        public readonly ?\DateTime $ended_at,

        public readonly int $max_reconnection_tries = 1,
        public readonly int $open_positions = 1,
        #[Rule([new Exists('organizations', 'id')])]
        public readonly ?int $organization_id = null,
    ) {

    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:250'],
            'description' => ['nullable', 'string', 'min:1', 'max:900'],
            'started_at' => ['nullable', 'date_format:Y-m-d H:m', 'after:now'],
            'ended_at' => ['nullable', 'date_format:Y-m-d H:m', 'after:started_at'],
            'max_reconnection_tries' => ['required', 'min:0', 'max:5'],
            'open_positions' => ['required', 'integer', 'min:1'],
        ];
    }
}
