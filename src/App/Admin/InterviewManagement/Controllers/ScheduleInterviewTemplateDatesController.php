<?php

namespace App\Admin\InterviewManagement\Controllers;

use App\Admin\InterviewManagement\Requests\ScheduleInterviewTemplateDatesRequest;
use App\Admin\InterviewManagement\Resources\InterviewTemplateResource;
use Domain\InterviewManagement\Actions\ScheduleInterviewTemplateDatesAction;
use Domain\InterviewManagement\DataTransferObjects\InterviewTemplateSettingsDto;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Support\Controllers\Controller;

class ScheduleInterviewTemplateDatesController extends Controller
{
    public function __invoke(InterviewTemplate $interview_template,ScheduleInterviewTemplateDatesRequest $request): InterviewTemplateResource
    {
        $result = (new ScheduleInterviewTemplateDatesAction($interview_template,InterviewTemplateSettingsDto::from([
            'started_at'    => $request->date('started_at'),
            'ended_at'    => $request->date('ended_at'),
        ])))->execute();

        return InterviewTemplateResource::make($result);
    }
}
