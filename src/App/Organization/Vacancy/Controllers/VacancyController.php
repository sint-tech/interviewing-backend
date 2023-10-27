<?php

namespace App\Organization\Vacancy\Controllers;

use App\Organization\Vacancy\Requests\VacancyStoreRequest;
use App\Organization\Vacancy\Resources\VacancyResource;
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
        return VacancyResource::make(
            $vacancy
        );
    }

    public function store(VacancyStoreRequest $request, CreateVacancyAction $action): VacancyResource
    {
        $dto = VacancyDto::from(
            $request->validated() + ['organization_id' => auth()->user()->organization_id, 'creator' => auth()->user()]);

        return VacancyResource::make(
            $action->execute($dto)
        );
    }

    public function update(Vacancy $vacancy)
    {

    }

    public function destroy(Vacancy $vacancy)
    {
        $vacancy->delete();

        return VacancyResource::make($vacancy);
    }
}
