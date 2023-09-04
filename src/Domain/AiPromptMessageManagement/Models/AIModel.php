<?php

namespace Domain\AiPromptMessageManagement\Models;

use Domain\AiPromptMessageManagement\Enums\AiModelEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property AiModelEnum $name
 */
class AIModel extends Model
{
    use HasFactory;

    protected $table = 'ai_models';

    protected $fillable = [
        'name',
        'status',
        'default_prompt_message',
    ];


    protected $casts = [
        'name' => AiModelEnum::class,
    ];

    public function aiPromptMessages(): HasMany
    {
        return $this->hasMany(AiPromptMessage::class,'ai_model_id');
    }
}
