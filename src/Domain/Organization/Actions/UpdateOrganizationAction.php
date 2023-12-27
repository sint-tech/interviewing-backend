<?php

namespace Domain\Organization\Actions;

use Domain\Organization\DataTransferObjects\OrganizationData;
use Domain\Organization\Models\Organization;

class UpdateOrganizationAction
{
    public function __construct(
        protected Organization $organization,
        protected OrganizationData $organizationData
    ) {
    }

    public function execute(): Organization
    {
        $this->organization->update(
            $this->organizationData->except('logo')->toArray()
        );

        return $this->organization->refresh();
    }
}
