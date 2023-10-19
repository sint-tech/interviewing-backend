<?php

namespace App\Organization\Auth\Controllers;

use App\Organization\Auth\Factories\EmployeeDataFactory;
use App\Organization\Auth\Factories\OrganizationDataFactory;
use App\Organization\Auth\Requests\RegisterRequest;
use App\Organization\Auth\Resources\AuthEmployeeResource;
use Domain\Organization\Actions\CreateOrganizationAction;
use Domain\Organization\Actions\GenerateEmployeeAccessTokenAction;
use Support\Controllers\Controller;

class RegisterController extends Controller
{
    public function __construct(
        public OrganizationDataFactory $organizationDataFactory,
        public EmployeeDataFactory $employeeDataFactory,
        public CreateOrganizationAction $createOrganizationAction,
        public GenerateEmployeeAccessTokenAction $generateEmployeeAccessTokenAction,
    ) {
    }

    public function __invoke(RegisterRequest $registerRequest)
    {
        $manager = $this->createOrganizationAction->execute(
            $this->organizationDataFactory->fromRequest($registerRequest),
            $this->employeeDataFactory->fromRequest($registerRequest),
        )->oldestManager;

        $token = $this->generateEmployeeAccessTokenAction->execute($manager);

        return AuthEmployeeResource::make($manager->load('organization'))->additional(['token' => $token]);
    }
}
