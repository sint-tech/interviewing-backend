<?php

namespace Domain\Vacancy\Actions;

use Domain\Vacancy\DataTransferObjects\VacancyDto;
use Domain\Vacancy\Models\Vacancy;

class UpdateVacancyAction
{
    public function execute(Vacancy $vacancy, VacancyDto $dto): Vacancy
    {
        $vacancy->update($dto->except('creator')->toArray());

        if ($vacancy->wasChanged('started_at')) {
            $vacancy->invitations()->where('should_be_invited_at', '<', $vacancy->started_at)->update(['should_be_invited_at' => $vacancy->started_at]);
        }

        return $vacancy->refresh();
    }
}
