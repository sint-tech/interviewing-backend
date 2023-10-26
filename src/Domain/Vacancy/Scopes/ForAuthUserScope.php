<?php

namespace Domain\Vacancy\Scopes;

use Domain\Organization\Models\Employee;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ForAuthUserScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  Builder  $builder,
     * @param  Vacancy  $model
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->user() instanceof Employee) {
            $builder->where('organization_id', auth()->user()->organization_id);
        }
    }
}
