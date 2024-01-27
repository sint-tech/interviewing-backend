<?php

namespace Support\Actions;

use App\Exceptions\ModelHasRelationsPreventDeleteException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PreventDeleteModelWhenHasRelationsAction
{
    protected bool $throwException = true;

    public function execute(Model $deletingModel, string|array $relations): bool
    {
        $deletingModel->load($relations);

        $foundedRelations = [];

        foreach ($relations as $relation => $builder) {
            if (is_int($relation)) {
                $relation = $builder;
            }

            if ($this->relationFounded($deletingModel, $relation)) {
                $foundedRelations[] = $relation;
            }
        }

        if ($this->throwException && count($foundedRelations)) {
            ModelHasRelationsPreventDeleteException::make($deletingModel, $relations);
        }

        return count($foundedRelations) > 0;
    }

    public function preventThrowException(bool $prevent = true): self
    {
        $this->throwException = ! $prevent;

        return $this;
    }

    protected function relationFounded(Model $deletingModel, string $relation): bool
    {
        $result = data_get($deletingModel, $relation);

        return match (true) {
            $result instanceof Collection => $result->isNotEmpty(),
            default => ! is_null($result)
        };
    }
}
