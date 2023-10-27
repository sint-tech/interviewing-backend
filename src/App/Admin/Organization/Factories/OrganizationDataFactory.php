<?php

namespace App\Admin\Organization\Factories;

use App\Admin\Organization\Requests\OrganizationStoreRequest;
use Domain\Organization\DataTransferObjects\OrganizationData;
use Spatie\LaravelData\Optional;

class OrganizationDataFactory
{
    public static function fromRequest(OrganizationStoreRequest $request)
    {
        return OrganizationData::from([
            'name' => $request->validated('name'),
            'logo' => $request->validated('logo', Optional::create()),
        ]);
    }
}
