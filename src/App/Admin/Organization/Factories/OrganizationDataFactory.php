<?php

namespace App\Admin\Organization\Factories;

use App\Admin\Organization\Requests\CreateOrganizationRequest;
use Domain\Organization\DataTransferObjects\OrganizationData;

class OrganizationDataFactory
{
    public static function fromRequest(CreateOrganizationRequest $request)
    {
        return OrganizationData::from([
            'name'  => $request->validated('name'),
        ]);
    }
}
