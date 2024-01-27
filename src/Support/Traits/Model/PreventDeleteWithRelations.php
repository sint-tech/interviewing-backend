<?php

namespace Support\Traits\Model;

use Illuminate\Database\Eloquent\Model;
use Support\Actions\PreventDeleteModelWhenHasRelationsAction;

/**
 * @mixin  Model
 */
trait PreventDeleteWithRelations
{
    public static function bootPreventDeleteWithRelations(): void
    {
        self::deleting(function (Model $model) {
            (new PreventDeleteModelWhenHasRelationsAction())->execute($model, $model->getPreventDeletionRelations());
        });
    }

    protected function getPreventDeletionRelations(): array
    {
        return [];
    }
}
