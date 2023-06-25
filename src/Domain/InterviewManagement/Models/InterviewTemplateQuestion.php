<?php

namespace Domain\InterviewManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewTemplateQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'interview_template_id',
        'question_cluster_id',
        'question_variant_id',
    ];
}
