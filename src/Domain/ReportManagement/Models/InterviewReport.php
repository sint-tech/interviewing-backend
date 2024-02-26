<?php

namespace Domain\ReportManagement\Models;

use Domain\InterviewManagement\Models\Interview;
use Illuminate\Database\Eloquent\Builder;
use Support\Scopes\ForAuthScope;

class InterviewReport extends Report
{
    const DEFAULT_REPORT_NAME = '__DEFAULT_REPORT__';

    public function metaKeys(): array
    {
        return [
            'avg_score' => 0,
            'candidate_advices' => [],
            'impacts' => [],
            'question_clusters_stats' => [],
            'language_fluency_score' => 0,
            'recruiter_advices' => [],
        ];
    }

    protected static function booted()
    {
        static::addGlobalScope(ForAuthScope::make()->forCandidate(function (Builder $builder) {
            return $builder
                ->where(function (Builder $wheres) {
                    return $wheres
                        ->where('reportable_type', (new Interview())->getMorphClass())
                        ->whereIn('reportable_id', auth()->user()->interviews()->select('id'));
                });
        }));

        parent::booted();
    }
}
