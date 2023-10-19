<?php

namespace Domain\Organization\Builders;

use Domain\Organization\Models\Employee;
use Domain\Users\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class EmployeeBuilder extends Builder
{
    public function forUser(Authenticatable $authenticatable)
    {
        if ($authenticatable instanceof Employee) {

            return $this->where('organization_id', $authenticatable->organization_id);
        } elseif ($authenticatable instanceof User) {
            return $this;
        }

        throw new \Exception('unhandled user type');
    }

    public function forAuth(): self
    {
        return $this->forUser(auth()->user());
    }
}
