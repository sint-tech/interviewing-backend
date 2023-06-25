<?php

namespace Domain\QuestionManagement\Models;

use Database\Factories\QuestionVariantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionVariant extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'question_variants';

    protected $fillable = [
        'text',
        'description',
        'reading_time_in_seconds',
        'answering_time_in_seconds',
        'question_id',
        'creator_id',
        'creator_type',
        'owner_id',
        'owner_type',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function creator(): MorphTo
    {
        return $this->morphTo('creator');
    }

    public function owner(): MorphTo
    {
        return $this->morphTo('owner');
    }

    protected static function newFactory()
    {
        return new QuestionVariantFactory;
    }
}
