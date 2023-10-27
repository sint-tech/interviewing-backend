<?php

namespace Support\Traits\DTO;

use Illuminate\Contracts\Auth\Authenticatable;
use Mockery\Exception;
use Spatie\LaravelData\Data;

/** @mixin Data */
trait HasCreator
{
    private static string $model_id_key = 'creator_id';

    private static string $model_type_key = 'creator_type';

    /**
     * @throws \Exception
     */
    public function withCreator($creator): self
    {
        if ($creator instanceof Authenticatable) {
            $data = [$creator->getKey(), $creator::class];
        }

        if (count(func_get_args()) < 2 && ! isset($data)) {
            throw new Exception('2 variables expected to be passed creator_id and creator_type');
        }

        [$creator_id,$creator_type] = $data ?? func_get_args();

        $this->additional([
            $this->getCreatorIdKeyName() => $creator_id,
            $this->getCreatorTypeKeyName() => $creator_type,
        ]);

        return $this;
    }

    private function getCreatorIdKeyName(): string
    {
        return $this->creatorIdKeyName() ?? static::$model_type_key;
    }

    private function getCreatorTypeKeyName(): string
    {
        return $this->creatorTypeKeyName() ?? static::$model_id_key;
    }

    public function creatorIdKeyName(): ?string
    {
        return null;
    }

    public function creatorTypeKeyName(): ?string
    {
        return null;
    }
}
