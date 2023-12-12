<?php

namespace Domain\JobTitle\Actions;

use Domain\JobTitle\DataTransferObjects\JobTitleDto;
use Domain\JobTitle\Models\JobTitle;

class UpdateJobTitleAction
{
    public function execute(JobTitle $jobTitle, JobTitleDto $dto): JobTitle
    {
        $jobTitle->update($dto->toArray());

        return $jobTitle->refresh();
    }
}
