<?php

namespace App\Admin\Vacancy\Controllers;

use App\Admin\Vacancy\Requests\VacancyStoreRequest;
use App\Admin\Vacancy\Resources\VacancyResource;
use Domain\Vacancy\Actions\CreateVacancyAction;
use Domain\Vacancy\DataTransferObjects\VacancyDto;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class VacancyController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return VacancyResource::collection(
            Vacancy::query()->paginate(pagination_per_page())
        );
    }

    public function show(Vacancy $vacancy): VacancyResource
    {
        return VacancyResource::make($vacancy);
    }

    public function store(VacancyStoreRequest $request, CreateVacancyAction $createJobOpportunityAction): VacancyResource
    {
        $dto = VacancyDto::from($request->validated() + ['creator' => auth()->user()]);

        return VacancyResource::make(
            $createJobOpportunityAction->execute($dto)
        );
    }

    public function update()
    {
        //
    }

    public function destroy(Vacancy $vacancy): VacancyResource
    {
        $vacancy->delete();

        return VacancyResource::make($vacancy);
    }
}
