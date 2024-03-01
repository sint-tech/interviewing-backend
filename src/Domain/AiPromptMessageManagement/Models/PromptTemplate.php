<?php

namespace Domain\AiPromptMessageManagement\Models;

use Database\Factories\PromptTemplateFactory;
use Domain\AiPromptMessageManagement\Builders\PromptTemplateBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromptTemplate extends Model
{
    use HasFactory;

    protected $table = 'prompt_templates';

    protected $fillable = [
        'name',
        'text',
        'stats_text',
        'conclusion_text',
        'is_selected',
        'version',
    ];

    protected $casts = [
        'version' => 'int',
        'is_selected' => 'bool',
    ];

    protected static function newFactory(): PromptTemplateFactory
    {
        return new PromptTemplateFactory();
    }

    public function newEloquentBuilder($query): PromptTemplateBuilder
    {
        return new PromptTemplateBuilder($query);
    }
}
