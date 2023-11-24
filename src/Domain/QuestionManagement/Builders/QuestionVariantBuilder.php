<?php

namespace Domain\QuestionManagement\Builders;

use Domain\Organization\Models\Employee;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;

class QuestionVariantBuilder extends Builder
{
    public function forOrganizationEmployee(Employee $employee): self
    {
        return $this->where('organization_id', $employee->organization_id);
    }

    public function forSintAdmin(User $sintAdmin): self
    {
        return $this;
    }
}
