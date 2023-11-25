<?php

namespace Domain\AiPromptMessageManagement\Models;

use Domain\AiPromptMessageManagement\Api\GPT35AiModel;
use Domain\AiPromptMessageManagement\Enums\AiModelEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property AiModelEnum $model
 * @property AiModelEnum $name
 */
class AIModel extends Model
{
    use HasFactory;

    protected $table = 'ai_models';

    protected $fillable = [
        'name',
        'status',
    ];

    protected $casts = [
        'name' => AiModelEnum::class,
    ];

    public function aiPromptMessages(): HasMany
    {
        return $this->hasMany(AiPromptMessage::class, 'ai_model_id');
    }

    public function model(): Attribute
    {
        return Attribute::make(fn() => $this->name);
    }

    public function prompt(string $answerText): string|null
    {
        if(! $this->relationLoaded('prompt_message') && ! $this->prompt_message instanceof AiPromptMessage) {
            throw new \Exception('ai_prompt_message not loaded as a pivot');
        }

        return $this->model->prompt($this->prompt_message,$answerText);
    }
}
