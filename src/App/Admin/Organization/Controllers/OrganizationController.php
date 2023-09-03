<?php

namespace App\Admin\Organization\Controllers;

use App\Admin\Organization\Factories\OrganizationDataFactory;
use App\Admin\Organization\Factories\OrganizationManagerDataFactory;
use App\Admin\Organization\Queries\IndexOrganizationQuery;
use App\Admin\Organization\Requests\OrganizationStoreRequest;
use App\Admin\Organization\Resources\OrganizationResource;
use Domain\Organization\Actions\CreateOrganizationAction;
use Domain\Organization\Actions\DeleteOrganizationAction;
use Domain\Organization\Actions\UploadOrganizationLogoAction;
use Domain\Organization\Models\Organization;
use Support\Controllers\Controller;

class OrganizationController extends Controller
{
    public function index(IndexOrganizationQuery $query)
    {
        return OrganizationResource::collection($query->paginate((int) request()->input('per_page', 25)));
    }

    public function show(int $organization): OrganizationResource
    {
        return OrganizationResource::make(Organization::query()->findOrFail($organization));
    }

    public function store(OrganizationStoreRequest $request): OrganizationResource
    {
        $organization = (new CreateOrganizationAction(
            OrganizationDataFactory::fromRequest($request),
            OrganizationManagerDataFactory::fromRequest($request),
        ))->execute();

        return OrganizationResource::make($organization);
    }

    public function update()
    {
        //
    }

    public function destroy(int $organization): OrganizationResource
    {
        $deletedOrganization = (new DeleteOrganizationAction($organization))->execute();

        return OrganizationResource::make($deletedOrganization);
    }
}
