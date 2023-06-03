<?php

namespace App\Admin\Organization\Factories;

use App\Admin\Organization\Requests\CreateOrganizationRequest;
use Domain\Organization\DataTransferObjects\EmployeeData;

class OrganizationManagerDataFactory
{
    public static function fromRequest(CreateOrganizationRequest $request)
    {
        $manager_key = 'manager';

        $validated_keys = [
            'first_name',
            'last_name',
            'email',
            'password',
        ];

        $data = [];

        foreach ($validated_keys as $validated_key) {
            $data[$validated_key] = $request->validated("$manager_key.$validated_key");
        }

        return EmployeeData::from($data);
    }
}
