<?php

namespace Domain\Vacancy\Actions;

use Domain\Vacancy\DataTransferObjects\VacancyDto;
use Domain\Vacancy\Models\Vacancy;

class CreateVacancyAction
{
    public function execute(VacancyDto $vacancyDto): Vacancy
    {
        $vacancy = new Vacancy(
            $vacancyDto->except('creator')->toArray() +
            [
                'creator_id' => $vacancyDto->creator->creator_id,
                'creator_type' => (new $vacancyDto->creator->creator_type())->getMorphClass(),
            ]);

        $vacancy->save();

        return $vacancy;
    }
}
