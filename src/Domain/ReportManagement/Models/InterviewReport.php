<?php

namespace Domain\ReportManagement\Models;

class InterviewReport extends Report
{
    public function metaKeys(): array
    {
        return [
            'avg_score' => 0,
            'advices' => [],
            'impacts' => [],
            'question_clusters_stats' => [],
        ];
    }
}
