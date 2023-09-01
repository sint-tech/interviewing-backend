<?php

namespace Domain\InterviewManagement\Models;

use Database\Factories\InterviewTemplateFactory;
use Domain\InterviewManagement\Enums\InterviewTemplateAvailabilityStatusEnum;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewTemplate extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'availability_status',
        'owner_id',
        'owner_type',
        'creator_id',
        'creator_type',
        'reusable',
    ];

    protected $casts = [
        'availability_status' => InterviewTemplateAvailabilityStatusEnum::class,
    ];

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class, 'interview_template_id');
    }

    public function owner(): MorphTo
    {
        return $this->morphTo('owner');
    }

    public function creator(): MorphTo
    {
        return $this->morphTo('creator');
    }

    public function questionVariants(): BelongsToMany
    {
        return $this->belongsToMany(
            QuestionVariant::class,
            'interview_template_questions',
            'interview_template_id',
            'question_variant_id'
        )->withTimestamps();
    }

    protected static function newFactory(): InterviewTemplateFactory
    {
        return new InterviewTemplateFactory();
    }
}
