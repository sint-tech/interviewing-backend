<?php

namespace Domain\JobTitle\Actions;

use Domain\JobTitle\DataTransferObjects\JobTitleDto;
use Domain\JobTitle\Models\JobTitle;

class CreateJobTitleAction
{
    public function execute(JobTitleDto $dto): JobTitle
    {
        return JobTitle::query()->create($dto->toArray());
    }
}
