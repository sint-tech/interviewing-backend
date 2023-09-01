<?php

namespace Domain\ReportManagement\DataTransferObjects;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;

class ReportDto extends Data
{
    /**
     * @throws \Exception
     */
    public function __construct(
        public readonly string $name,
        public readonly Model $reportable,
        public readonly array $values
    ) {
        if (! in_array('Domain\ReportManagement\Traits\HasReport', class_uses($this->reportable))) {
            throw new \Exception('reportable object should use HasReport Trait!');
        }

        $this->validateValuesAreReportValueDto();
    }

    /**
     * @throws \Exception
     */
    protected function validateValuesAreReportValueDto(): void
    {
        foreach ($this->values as $value) {
            if (! $value instanceof ReportValueDto) {
                throw new \Exception('all values array should be instance of Domain\\ReportManagement\\DataTransferObjects\\ReportValueDto');
            }
        }
    }
}
