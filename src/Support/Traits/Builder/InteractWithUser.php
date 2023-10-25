<?php

namespace Support\Traits\Builder;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;

/** @mixin Builder */
trait InteractWithUser
{
    abstract public function forUser(Authenticatable $authenticated): self;

    public function forAuth(): self
    {
        return $this->forUser(auth()->user());
    }
}
