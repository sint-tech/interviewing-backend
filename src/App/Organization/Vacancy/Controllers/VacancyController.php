<?php

namespace App\Organization\Vacancy\Controllers;

use App\Organization\Vacancy\Requests\VacancyStoreRequest;
use App\Organization\Vacancy\Requests\VacancyUpdateRequest;
use App\Organization\Vacancy\Resources\VacancyResource;
use Domain\Vacancy\Actions\CreateVacancyAction;
use Domain\Vacancy\Actions\UpdateVacancyAction;
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

    public function show(int $vacancy): VacancyResource
    {
        return VacancyResource::make(
            Vacancy::query()->findOrFail($vacancy)->load('interviewTemplate')
        );
    }

    public function store(VacancyStoreRequest $request, CreateVacancyAction $action): VacancyResource
    {
        $dto = VacancyDto::from(
            $request->validated() + ['organization_id' => auth()->user()->organization_id, 'creator' => auth()->user()]);

        return VacancyResource::make(
            $action->execute($dto)->load(['interviewTemplate'])
        );
    }

    public function update(Vacancy $vacancy, VacancyUpdateRequest $request): VacancyResource
    {
        if (empty($request->safe()->all())) {
            return VacancyResource::make($vacancy);
        }

        $dto = VacancyDto::from(
            array_merge($vacancy->attributesToArray(), $request->validated())
        );

        return VacancyResource::make(
            (new UpdateVacancyAction())->execute($vacancy, $dto)
        );
    }

    public function destroy(Vacancy $vacancy)
    {
        $vacancy->delete();

        return VacancyResource::make($vacancy);
    }
}
