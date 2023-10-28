<?php

namespace Domain\Vacancy\Actions;

use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Vacancy\DataTransferObjects\VacancyDto;
use Domain\Vacancy\Models\Vacancy;

class CreateVacancyAction
{
    public function execute(VacancyDto $vacancyDto): Vacancy
    {
        //ensure interview template exists
        InterviewTemplate::query()
            ->whereKey($vacancyDto->interview_template_id)
            ->existsOr(fn () => throw new \Exception('the interview template is not available now.'));

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
