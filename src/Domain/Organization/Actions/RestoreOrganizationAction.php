<?php

namespace Domain\Organization\Actions;

use Domain\Organization\Models\Organization;

class RestoreOrganizationAction
{
    /**
     * @throws \Exception
     */
    public function execute(Organization $organization): Organization
    {
        if (! $organization->trashed()) {
            throw new \Exception("organization with id: {$organization->getKey()} already exists");
        }

        $organization->restore();

        return $organization->refresh();
    }
}
