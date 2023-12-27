<?php

namespace App\Organization\Settings\Controllers;

use App\Organization\Auth\Resources\OrganizationResource;
use App\Organization\Settings\Requests\OrganizationUpdateRequest;
use Domain\Organization\Actions\UpdateOrganizationAction;
use Domain\Organization\DataTransferObjects\OrganizationData;
use Support\Controllers\Controller;

class UpdateOrganizationProfileController extends Controller
{
    public function __invoke(OrganizationUpdateRequest $request): OrganizationResource
    {
        $data = OrganizationData::from(array_merge(auth()->user()->organization->toArray(), $request->safe()->except('logo')));

        return OrganizationResource::make(
            (new UpdateOrganizationAction(auth()->user()->organization, $data))->execute()
        );
    }
}
