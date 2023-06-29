<?php

namespace Domain\InterviewManagement\Models;

use Domain\InterviewManagement\Enums\QuestionOccurrenceReasonEnum;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Pivot
{
    use HasFactory,SoftDeletes;

    public $incrementing = true;

    protected $table = 'interview_answers';

    protected $fillable = [
        'question_occurrence_reason',
        'answer_text',
        'score',
        'min_score',
        'max_score',
        'interview_id',
        'answer_variant_id',
        'question_variant_id',
        'ml_video_semantics',
        'ml_audio_semantics',
        'ml_text_semantics'
    ];

    protected $casts = [
        'question_occurrence_reason' => QuestionOccurrenceReasonEnum::class,
        'ml_video_semantics'         => 'json',
        'ml_audio_semantics'         => 'json',
        'ml_text_semantics'         => 'json',
    ];

    public function questionVariant(): BelongsTo
    {
        return $this->belongsTo(QuestionVariant::class,'question_variant_id');
    }

    public function interview():BelongsTo
    {
        return $this->belongsTo(Interview::class,'interview_id');
    }
}
