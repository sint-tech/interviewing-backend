<?php

namespace Domain\InterviewManagement\Models;

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
    ];

    public function interview():BelongsTo
    {
        return $this->belongsTo(Interview::class,'interview_id');
    }
}
