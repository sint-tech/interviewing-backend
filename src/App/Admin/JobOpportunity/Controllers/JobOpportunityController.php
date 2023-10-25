<?php

namespace App\Admin\JobOpportunity\Controllers;

use App\Admin\JobOpportunity\Requests\JobOpportunityStoreRequest;
use App\Admin\JobOpportunity\Resources\JobOpportunityResource;
use Domain\Vacancy\Actions\CreateVacancyAction;
use Domain\Vacancy\DataTransferObjects\JobOpportunityDto;
use Support\Controllers\Controller;

class JobOpportunityController extends Controller
{
    public function index()
    {
        //
    }

    public function show()
    {
        //
    }

    public function store(JobOpportunityStoreRequest $request, CreateVacancyAction $createJobOpportunityAction): JobOpportunityResource
    {
        $dto = JobOpportunityDto::from($request->validated())
            ->withCreator(auth()->user());

        return JobOpportunityResource::make(
            $createJobOpportunityAction->execute($dto)
        );
    }

    public function update()
    {
        //
    }

    public function destroy()
    {
        //
    }
}
