<?php

namespace Domain\Organization\Actions;

use Domain\Organization\Models\Employee;
use Domain\Organization\Models\Organization;

class DeleteOrganizationAction
{
    public function __construct
    (
        public int $organizationId
    )
    {
    }

    public function execute(): Organization
    {
        $deletedOrganization = Organization::query()->findOrFail($this->organizationId);

        $deletedOrganization->employees()->each(fn(Employee $employee) => $employee->delete());

        $deletedOrganization->delete();

        return $deletedOrganization;
    }
}
