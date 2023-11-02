<?php

namespace Support\Scopes;

use Domain\Candidate\Models\Candidate;
use Domain\Organization\Models\Employee;
use Domain\Users\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ForAuthScope implements Scope
{
    protected array $buildersBerUser = [];

    const GUEST_KEY = '__GUEST__';

    protected bool $shouldApplyGuestBuilder = false;

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @throws AuthorizationException
     * @throws \Throwable
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole() && ! auth()->runningUnitTests()) {
            return;
        }

        if (auth()->guest() && ! isset($this->buildersBerUser[self::GUEST_KEY])) {
            return;
        }

        $key = auth()->guest() ? self::GUEST_KEY : $this->modelKey(auth()->user());

        if (! isset($this->buildersBerUser[$key])) {
            return;
        }

        $closure = $this->buildersBerUser[$key];

        $closure($builder);
    }

    public function forOrganizationEmployee(\Closure $builder): self
    {
        $this->forAuthUser(Employee::class, $builder);

        return $this;
    }

    public function forSintUser(\Closure $builder): self
    {
        $this->forAuthUser(User::class, $builder);

        return $this;
    }

    public function forCandidate(\Closure $builder): self
    {
        $this->forAuthUser(Candidate::class, $builder);

        return $this;
    }

    public function forGuest(\Closure $builder): self
    {
        $this->buildersBerUser[self::GUEST_KEY] = $builder;

        return $this;
    }

    private function forAuthUser(string|Authenticatable $user, \Closure $builder): void
    {
        if ($user instanceof Authenticatable) {
            $user = $user::class;
        }

        $this->buildersBerUser[$this->modelKey($user)] = $builder;
    }

    private function modelKey(string|Authenticatable $user): string
    {
        return str($user instanceof Authenticatable ? $user::class : $user)
            ->afterLast('\\')
            ->lower()
            ->toString();
    }
}
