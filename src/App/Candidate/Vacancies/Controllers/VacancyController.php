<?php

namespace App\Candidate\Vacancies\Controllers;

use App\Candidate\Vacancies\Resources\VacancyResource;
use Domain\Vacancy\Models\Vacancy;

class VacancyController
{
    public function index()
    {
        return VacancyResource::collection(Vacancy::query()
            ->with(['interviewTemplate.questionClusters.skills', 'organization'])
            ->wherePublic()
            ->whereNotNull('slug')
            ->whereSlugLike(request('slug'))
            ->paginate(pagination_per_page()));
    }

    public function show(string $vacancy)
    {
        return VacancyResource::make(Vacancy::query()
            ->with(['interviewTemplate.questionClusters.skills', 'organization'])
            ->wherePublic()
            ->whereNotNull('slug')
            ->whereSlug($vacancy)
            ->firstOrFail());
    }
}
