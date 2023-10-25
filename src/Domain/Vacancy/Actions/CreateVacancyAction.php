<?php

namespace Domain\Vacancy\Actions;

use Domain\Vacancy\DataTransferObjects\JobOpportunityDto;
use Domain\Vacancy\Models\JobOpportunity;

class CreateVacancyAction
{
    public function execute(JobOpportunityDto $jobOpportunityDto): JobOpportunity
    {
        $jobOpportunity = new JobOpportunity($jobOpportunityDto->toArray());

        $jobOpportunity->save();

        return $jobOpportunity;
    }
}
