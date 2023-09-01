<?php

namespace Domain\ReportManagement\Traits;

use Domain\ReportManagement\Models\Report;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasReport
{
    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function latestReport(): MorphOne
    {
        return $this->morphOne(Report::class, 'reportable')->latest();
    }

    public function oldestReport(): MorphOne
    {
        return $this->morphOne(Report::class, 'reportable')->oldest();
    }
}
