<?php

namespace Domain\ReportManagement\Actions;

use Domain\ReportManagement\DataTransferObjects\ReportDto;
use Domain\ReportManagement\Models\Report;

class CreateReportAction
{
    public function __construct(
        protected ReportDto $reportDto
    ) {
    }

    public function execute(): Report
    {
        if (! in_array('Domain\ReportManagement\Traits\HasReport', class_uses($this->reportDto->reportable))) {
            throw new \Exception('reportable object should use HasReport Trait!');
        }

        $report = (new Report());

        $report->fill([
            'name' => $this->reportDto->name,
            'reportable_type' => $this->reportDto->reportable->getMorphClass(),
            'reportable_id' => $this->reportDto->reportable->getKey(),
        ]);

        $metas = [];

        foreach ($this->reportDto->values as $value) {
            $metas[$value->key] = $value->value;
        }

        $report->setMeta($metas);

        $report->save();

        return $report;
    }
}
