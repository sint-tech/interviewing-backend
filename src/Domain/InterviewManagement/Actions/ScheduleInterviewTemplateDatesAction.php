<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\DataTransferObjects\InterviewTemplateSettingsDto;
use Domain\InterviewManagement\Models\InterviewTemplate;

class ScheduleInterviewTemplateDatesAction
{
    public function __construct(
        public InterviewTemplate $interviewTemplate,
        public readonly InterviewTemplateSettingsDto $interviewTemplateSettingsDto
    ) {
    }

    public function execute(): InterviewTemplate
    {
        $this->interviewTemplate->settings()->apply(
            $this->interviewTemplateSettingsDto->except('max_reconnection_tries')->toArray()
        );

        return $this->interviewTemplate->refresh();
    }
}
