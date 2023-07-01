<?php

namespace Domain\QuestionManagement\Models;

use Domain\QuestionManagement\Enums\AiModelEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiPromptMessage extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'prompt_text',
        'ai_model',
        'question_variant_id',
        'is_default'
    ];

    protected $casts = [
        'ai_model'  => AiModelEnum::class,
    ];

    public function questionVariant(): BelongsTo
    {
        return $this->belongsTo(
            QuestionVariant::class,
            'question_variant_id',
            'id'
        );
    }
}
