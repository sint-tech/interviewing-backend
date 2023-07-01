<?php

namespace Domain\InterviewManagement\Models;

use Domain\QuestionManagement\Models\Question;
use Domain\QuestionManagement\Models\QuestionCluster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class InterviewTemplateQuestion extends Pivot
{
    use HasFactory;

    protected $table = 'interview_template_questions';

    protected $fillable = [
        'interview_template_id',
        'question_cluster_id',
        'question_variant_id',
    ];

    protected $with = [
        'questionVariant',
        'questionCluster',
    ];

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class, 'interview_template_id', 'interview_template_id');
    }

    public function interviewTemplate(): BelongsTo
    {
        return $this->belongsTo(InterviewTemplate::class, 'interview_template_id');
    }

    public function questionVariant(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_variant_id');
    }

    public function questionCluster(): BelongsTo
    {
        return $this->belongsTo(QuestionCluster::class, 'question_cluster_id');
    }
}
