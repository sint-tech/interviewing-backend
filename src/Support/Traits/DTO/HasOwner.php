<?php

namespace Support\Traits\DTO;

use Illuminate\Database\Eloquent\Model;
use Mockery\Exception;
use Spatie\LaravelData\Data;

/** @mixin Data */
trait HasOwner
{
    /**
     * @throws \Throwable
     */
    public function WithOwner($creator): self
    {
        if ($creator instanceof Model) {
            $this->additional([
                'owner_id' => $creator->getKey(),
                'owner_type' => $creator::class,
            ]);

            return $this;
        }

        $data = func_get_args();

        if (count($data) < 2) {
            throw new Exception('2 variables expected to be passed owner_id and owner_type');
        }

        $this->additional([
            'owner_id' => $data[0],
            'owner_type' => $data[1],
        ]);

        return $this;
    }
}
