<?php

namespace Domain\AnswerManagement\Models;

use Database\Factories\AnswerFactory;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'min_score',
        'max_score',
        'question_variant_id',
    ];

    public function answerVariants(): HasMany
    {
        return $this->hasMany(AnswerVariant::class, 'answer_id');
    }

    public function questionVariant(): BelongsTo
    {
        return $this->belongsTo(QuestionVariant::class, 'question_variant_id');
    }

    protected static function newFactory(): AnswerFactory
    {
        return new AnswerFactory();
    }
}
