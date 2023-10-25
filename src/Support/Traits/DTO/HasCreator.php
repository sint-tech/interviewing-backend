<?php

namespace Support\Traits\DTO;

use Illuminate\Contracts\Auth\Authenticatable;
use Mockery\Exception;
use phpDocumentor\Reflection\Types\Array_;
use Spatie\LaravelData\Data;

/** @mixin Data */
trait HasCreator
{
    /**
     * @throws \Exception
     */
    public function withCreator($creator):self
    {
        if ($creator instanceof Authenticatable) {
            $this->additional([
                'creator_id'  => $creator->getKey(),
                'creator_type' => $creator::class
            ]);

            return $this;
        }

        $data = func_get_args();

        if (count($data) < 2) {
            throw new Exception('2 variables expected to be passed creator_id and creator_type');
        }

        $this->additional([
            'creator_id' => $data[0],
            'creator_type' => $data[1]
        ]);

        return $this;
    }
}
