<?php

namespace Domain\QuestionManagement\Models;

use Domain\QuestionManagement\Enums\AiModelEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromptMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_variant_id',
        'prompt_text',
        'ai_model',
    ];

    protected $casts = [
        'model' => AiModelEnum::class
    ];
}
