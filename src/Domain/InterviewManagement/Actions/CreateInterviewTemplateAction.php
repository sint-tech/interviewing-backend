<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\DataTransferObjects\InterviewTemplateDto;
use Domain\InterviewManagement\Models\InterviewTemplate;

class CreateInterviewTemplateAction
{
    public function __construct
    (
        public readonly InterviewTemplateDto $interviewTemplateDto,
    )
    {

    }

    public function execute():InterviewTemplate
    {
        $interviewTemplate = (new InterviewTemplate())->fill(
            $this->interviewTemplateDto->toArray()
        );

        $interviewTemplate->save();

        return $interviewTemplate->refresh();
    }
}
