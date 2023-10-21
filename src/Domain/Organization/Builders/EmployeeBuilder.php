<?php

namespace Domain\Organization\Builders;

use Domain\Organization\Models\Employee;
use Domain\Users\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class EmployeeBuilder extends Builder
{
    /**
     * @throws \Exception
     */
    public function forUser(Authenticatable $authenticated)
    {
        if ($authenticated instanceof Employee) {
            return $this->where('organization_id', $authenticated->organization_id);
        } elseif ($authenticated instanceof User) {
            return $this;
        }

        throw new \Exception('unhandled user type');
    }

    public function forAuth(): self
    {
        return $this->forUser(auth()->user());
    }
}
