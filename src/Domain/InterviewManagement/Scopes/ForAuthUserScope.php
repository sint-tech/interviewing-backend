<?php

namespace Domain\InterviewManagement\Scopes;

use Domain\Organization\Models\Employee;
use Domain\Organization\Models\Organization;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ForAuthUserScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  Builder  $builder ,
     * @param  Vacancy  $model
     *
     * @throws AuthorizationException
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (! app()->runningInConsole() && ! auth()->hasUser()) {
            throw new AuthorizationException('forbidden');
        }

        if (auth()->user() instanceof Employee) {
            $builder->whereIn('owner_type', [Organization::class, auth()->user()->organization->getMorphClass()])
                ->where('owner_id', auth()->id());
        }
    }
}
