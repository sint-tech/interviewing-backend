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
        return $this->where(function ($query) {
            $query->whereHas('vacancy', function ($query) {
                $query->where('ended_at', '<=', now());
            })->orWhere('expired_at', '<=', now())
                ->orWhereNotNull('used_at');
        });
    }

    public function whereIsNotExpired(): self
    {
        return $this->where(function ($query) {
            $query->whereHas('vacancy', function ($query) {
                $query->where('ended_at', '>', now());
            })
            ->where(function ($query) {
                $query->where('expired_at', '>', now())
                    ->orWhereNull('used_at');
            });
        });
    }
}
