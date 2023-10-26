<?php

namespace Domain\ReportManagement\Actions;

use Domain\ReportManagement\DataTransferObjects\ReportDto;
use Domain\ReportManagement\Models\Report;

class CreateReportAction
{
    public function __construct(
    ) {
    }

    public function execute(
        ReportDto $reportDto
    ): Report {
        if (! in_array('Domain\ReportManagement\Traits\HasReport', class_uses($reportDto->reportable))) {
            throw new \Exception('reportable object should use HasReport Trait!');
        }

        $report = (new Report());

        $report->fill([
            'name' => $reportDto->name,
            'reportable_type' => $reportDto->reportable->getMorphClass(),
            'reportable_id' => $reportDto->reportable->getKey(),
        ]);

        $metas = [];

        foreach ($reportDto->values as $value) {
            $metas[$value->key] = $value->value;
        }

        $report->setMeta($metas);

        $report->save();

        return $report;
    }
}
