<?php

namespace Support\Traits\Model;

use Illuminate\Database\Eloquent\Model;
use Support\ValueObjects\BatchValueObject;

/**
 *@mixin Model
 */
trait HasBatch
{
    private static string $BATCH_COLUMN_NAME = 'batch';

    public static function bootHasBatch(): void
    {
        self::creating(function (self $model) {
            $model->{$model->getBatchColumnName()} = BatchValueObject::getInstance($model)->getNextBatch();
        });
    }

    public function bathColumnName(): string
    {
        return self::$BATCH_COLUMN_NAME;
    }

    private function getBatchColumnName(): string
    {
        return $this->bathColumnName();
    }
}
