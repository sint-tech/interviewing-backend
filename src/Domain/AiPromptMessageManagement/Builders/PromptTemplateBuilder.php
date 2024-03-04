<?php

namespace Domain\AiPromptMessageManagement\Builders;

use Closure;
use Domain\AiPromptMessageManagement\Models\PromptTemplate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PromptTemplateBuilder extends Builder
{
    /**
     * @throwS ModelNotFoundException<PromptTemplate>
     */
    public function latestTemplateOrFail(string $name): ?PromptTemplate
    {
        /** @var null|PromptTemplate */
        return $this->where('is_selected', true)
            ->where('name', $name)
            ->latest('version')
            ->firstOrFail();
    }

    /**
     * @param string $name
     * @param Closure|null $callback
     * @return PromptTemplate|mixed
     */
    public function latestTemplateOr(string $name, Closure|null $callback = null)
    {
        return $this->where('is_selected', true)
            ->where('name', $name)
            ->latest('version')
            ->firstOr($callback);
    }
}
