<?php

namespace Support\Traits\DTO;

use Illuminate\Contracts\Auth\Authenticatable;
use Mockery\Exception;
use Spatie\LaravelData\Data;

/** @mixin Data */
trait HasCreator
{
    /**
     * @throws \Exception
     */
    public function withCreator($creator): self
    {
        if ($creator instanceof Authenticatable) {
            $this->additional([
                'creator_id' => $creator->getKey(),
                'creator_type' => $creator::class,
            ]);

            return $this;
        }

        if (count($data = func_get_args()) < 2) {
            throw new Exception('2 variables expected to be passed creator_id and creator_type');
        }

        [$creator_id,$creator_type] = $data;

        $this->additional(compact($creator_id, $creator_type));

        return $this;
    }
}
