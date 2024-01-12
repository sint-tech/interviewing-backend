<?php

namespace Domain\Vacancy\Queries;

use Domain\Vacancy\Models\Vacancy;

class TotalVacanciesQuery
{
    public function execute(): int
    {
        return Vacancy::query()->count();
    }
}
