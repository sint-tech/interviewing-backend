<?php

namespace Domain\Organization\Actions;

use Domain\Organization\DataTransferObjects\EmployeeData;
use Domain\Organization\DataTransferObjects\OrganizationData;
use Domain\Organization\Models\Organization;
use Illuminate\Http\UploadedFile;

class CreateOrganizationAction
{
    public function __construct(
        private CreateEmployeeAction $createEmployeeAction
    ) {

    }

    public function execute(
        OrganizationData $organizationData,
        EmployeeData $employeeData
    ): Organization {
        $organization = (new Organization());

        $organization->fill($organizationData->toArray());

        $organization->save();

        $this->createEmployeeAction->execute($employeeData->additional([
            'password' => $employeeData->password,
            'is_organization_manager' => true,
            'organization_id' => $organization->getKey(),
        ]));

        if ($organizationData->logo instanceof UploadedFile) {
            (new UploadOrganizationLogoAction($organization, $organizationData->logo))->execute();
        }

        return $organization->refresh()->load(['oldestManager', 'logo']);
    }
}
