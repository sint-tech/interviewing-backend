<?php

namespace Support\Traits\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/** @mixin Model */
trait HasOwner
{
    public function owner(): MorphTo
    {
        return $this->morphTo('owner');
    }
}
