<?php

namespace Domain\InterviewManagement\Models;

use Domain\Candidate\Models\Candidate;
use Domain\QuestionManagement\Models\QuestionCluster;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interview extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'interview_template_id',
        'candidate_id',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function interviewTemplate(): BelongsTo
    {
        return $this->belongsTo(InterviewTemplate::class, 'interview_template_id');
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function interviewTemplateQuestions():HasMany
    {
        return $this->hasMany(
            InterviewTemplateQuestion::class,
            'interview_template_id',
            'interview_template_id'
        );
    }

    public function questionVariants():BelongsToMany
    {
        return $this->belongsToMany(
            QuestionVariant::class,
            'interview_template_questions',
            'interview_template_id',
            'question_variant_id',
            'interview_template_id'

        )
        ->using(InterviewTemplateQuestion::class)
        ->withPivot('question_cluster_id')
        ->withTimestamps();
    }

    public function questionClusters():BelongsToMany
    {
        return $this->belongsToMany(
            QuestionCluster::class,
            'interview_template_questions',
            'interview_template_id',
            'question_cluster_id',
            'interview_template_id'

        )
        ->using(InterviewTemplateQuestion::class)
        ->withPivot('question_variant_id')
        ->withTimestamps();
    }
}
