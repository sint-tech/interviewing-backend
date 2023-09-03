<?php

namespace Domain\Organization\Actions;

use Domain\Organization\DataTransferObjects\EmployeeData;
use Domain\Organization\DataTransferObjects\OrganizationData;
use Domain\Organization\Models\Organization;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

class CreateOrganizationAction
{
    public function __construct(
        public OrganizationData $organizationData,
        public EmployeeData $employeeData
    ) {

    }

    public function execute(): Organization
    {
        $organization = (new Organization());

        $organization->fill($this->organizationData->toArray());

        $organization->save();

        $employeeData = $this->employeeData->toArray();

        $employeeData = array_merge($employeeData,
            [
                'password' => Hash::make($employeeData['password']),
                'is_organization_manager' => true,
                'organization_id' => $organization->getKey(),
            ]);

        $organization->oldestManager()->create($employeeData);

        if ($this->organizationData->logo instanceof UploadedFile) {
            (new UploadOrganizationLogoAction($organization,$this->organizationData->logo))->execute();
        }

        return $organization->refresh()->load(['oldestManager','logo']);
    }
}
