<?php

namespace Domain\Invitation\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin Builder
 */

class InvitationEloquentBuilder extends Builder
{
    public function whereIsExpired(): self
    {
        return $this->whereHas('vacancy', fn ($query) => $query->where('ended_at', '<', now()))
            ->orWhere('expired_at', '<', now());
    }

    public function whereIsNotExpired(): self
    {
        return $this->whereHas('vacancy', fn ($query) => $query->where('ended_at', '>', now()))
            ->where(fn ($query) => $query->where('expired_at', '>', now())->orWhereNull('expired_at'));
    }
}
