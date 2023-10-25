<?php

namespace Domain\Vacancy\Builders;

use Domain\Organization\Models\Employee;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Support\Traits\Builder\InteractWithUser;

class JobOpportunityBuilder extends Builder
{
    use InteractWithUser;

    public function getModels($columns = ['*'])
    {
        //todo use other way instead of getModels method
        if (app()->runningUnitTests() or ! app()->runningInConsole()) {
            $this->forAuth();
        }

        return parent::getModels($columns);
    }

    public function forUser(Authenticatable $authenticated): self
    {
        if ($authenticated instanceof Employee) {
            return $this->where('organization_id',$authenticated->organization_id);
        }

        return $this;
    }
}
