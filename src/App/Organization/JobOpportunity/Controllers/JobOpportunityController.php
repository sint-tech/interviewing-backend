<?php

namespace App\Organization\JobOpportunity\Controllers;

use App\Organization\JobOpportunity\Requests\JobOpportunityStoreRequest;
use App\Organization\JobOpportunity\Resources\JobOpportunityResource;
use Domain\Vacancy\Actions\CreateVacancyAction;
use Domain\Vacancy\DataTransferObjects\JobOpportunityDto;
use Domain\Vacancy\Models\JobOpportunity;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class JobOpportunityController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return JobOpportunityResource::collection(
            JobOpportunity::query()->paginate(pagination_per_page())
        );
    }

    public function show(int $job_opportunity): JobOpportunityResource
    {
        return JobOpportunityResource::make(
            JobOpportunity::query()->findOrFail($job_opportunity)
        );
    }

    public function store(JobOpportunityStoreRequest $request, CreateVacancyAction $action): JobOpportunityResource
    {
        $dto = JobOpportunityDto::from(
            $request->validated() + ['organization_id' => auth()->user()->organization_id]
        )->withCreator(auth()->user());

        return JobOpportunityResource::make(
            $action->execute($dto)
        );
    }

    public function update()
    {

    }

    public function destroy()
    {

    }
}
