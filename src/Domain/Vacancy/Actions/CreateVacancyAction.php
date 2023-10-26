<?php

namespace Domain\Vacancy\Actions;

use Domain\Vacancy\DataTransferObjects\VacancyDto;
use Domain\Vacancy\Models\Vacancy;

class CreateVacancyAction
{
    public function execute(VacancyDto $jobOpportunityDto): Vacancy
    {
        $jobOpportunity = new Vacancy($jobOpportunityDto->toArray());

        $jobOpportunity->save();

        return $jobOpportunity;
    }
}
