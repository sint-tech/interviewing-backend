<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\DataTransferObjects\InterviewDto;
use Domain\InterviewManagement\Models\Interview;

class CreateInterviewAction
{
    public function __construct(
        public readonly InterviewDto $interviewDto
    )
    {
    }

    public function execute():Interview
    {
        $interview = (new Interview())->fill($this->interviewDto->toArray());

        $interview->save();

        return $interview->refresh();
    }
}
