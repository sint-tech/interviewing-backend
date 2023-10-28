<?php

namespace Domain\Vacancy\DataTransferObjects;

use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Organization\Models\Organization;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithCastable;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Support\Data\Creator;

class VacancyDto extends Data
{
    //    use HasCreator;

    public function __construct(
        public readonly string $title,
        public readonly string|Optional|null $description = null,

        public readonly ?int $organization_id,
        public readonly int $interview_template_id,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i')]
        public readonly ?\DateTime $started_at,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i')]
        public readonly ?\DateTime $ended_at,

        public readonly int $max_reconnection_tries,
        public readonly int $open_positions,
        #[WithCastable(Creator::class, lazy_load_instance: true)]
        public Creator $creator,
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
            'organization_id' => ['nullable', 'int', Rule::exists(table_name(Organization::class, 'id'))->withoutTrashed()],
            'interview_template_id' => ['required', 'int', Rule::exists(table_name(InterviewTemplate::class, 'id'))->withoutTrashed()],
        ];
    }
}
