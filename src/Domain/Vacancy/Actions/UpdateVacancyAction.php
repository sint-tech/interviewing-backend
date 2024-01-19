<?php

namespace Domain\Vacancy\Actions;

use Domain\Vacancy\DataTransferObjects\VacancyDto;
use Domain\Vacancy\Models\Vacancy;

class UpdateVacancyAction
{
    public function execute(Vacancy $vacancy, VacancyDto $dto): Vacancy
    {
        $vacancy->update($dto->except('creator')->toArray());

        return $vacancy->refresh();
    }
}
