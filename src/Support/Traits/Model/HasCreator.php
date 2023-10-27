<?php

namespace Support\Traits\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/** @mixin Model */
trait HasCreator
{
    public function creator(): MorphTo
    {
        return $this->morphTo('creator');
    }
}
