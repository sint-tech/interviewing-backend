<?php

namespace Domain\InterviewManagement\DataTransferObjects;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

/**
 * @deprecated
 */
class InterviewTemplateSettingsDto extends Data
{
    public function __construct(
        public readonly ?Carbon $started_at = null,
        public readonly ?Carbon $ended_at = null,
        public readonly int $max_reconnection_tries = 1
    ) {
    }

    public static function defaultValues(): self
    {
        return self::from(config('model_settings.defaultSettings.interview_templates', []));
    }
}
