<?php

namespace Domain\ReportManagement\Models;

use Domain\InterviewManagement\Models\Interview;
use Illuminate\Database\Eloquent\Builder;
use Support\Scopes\ForAuthScope;
use Database\Factories\InterviewReportFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'emotional_score' => [],
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

    /**
     * Scope a query to only include reports of ended vacancies.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithEndedVacancy($query)
    {
        return $query->whereHas('reportable.vacancy', function ($query) {
            return $query->whereEnded();
        });
    }

    protected static function newFactory(): Factory
    {
        return InterviewReportFactory::new();
    }
}
