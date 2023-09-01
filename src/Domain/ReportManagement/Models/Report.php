<?php

namespace Domain\ReportManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Kodeine\Metable\Metable;

class Report extends Model
{
    use HasFactory,Metable;

    protected $metaTable = 'report_values';

    protected $metaKeyName = 'report_id';

    protected $fillable = [
        'name',
        'reportable_id',
        'reportable_type',
    ];

    public function reportable(): MorphTo
    {
        return $this->morphTo('reportable');
    }

    public function values(): HasMany
    {
        return $this->hasMany(ReportValue::class, 'report_id');
    }
}
