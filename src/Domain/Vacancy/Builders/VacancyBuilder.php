<?php

namespace Domain\Vacancy\Builders;

use Domain\Organization\Models\Employee;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Support\Traits\Builder\InteractWithUser;

class VacancyBuilder extends Builder
{
    use InteractWithUser;

    public function forUser(Authenticatable $authenticated): self
    {
        if ($authenticated instanceof Employee) {
            return $this->where('organization_id',$authenticated->organization_id);
        }

        return $this;
    }
}
