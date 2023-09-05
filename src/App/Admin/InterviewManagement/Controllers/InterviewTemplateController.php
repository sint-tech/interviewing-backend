<?php

namespace App\Admin\InterviewManagement\Controllers;

use App\Admin\InterviewManagement\Requests\InterviewTemplateStoreRequest;
use App\Admin\InterviewManagement\Resources\InterviewTemplateResource;
use Domain\InterviewManagement\Actions\CreateInterviewTemplateAction;
use Domain\InterviewManagement\DataTransferObjects\InterviewTemplateDto;
use Domain\InterviewManagement\DataTransferObjects\InterviewTemplateSettingsDto;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Support\Controllers\Controller;

class InterviewTemplateController extends Controller
{
    public function index()
    {
        return InterviewTemplateResource::collection(
            InterviewTemplate::query()->latest()->paginate(
                request()->integer('per_page', 30)
            )
        );
    }

    public function show(int $interview_template): InterviewTemplateResource
    {
        return InterviewTemplateResource::make(
            InterviewTemplate::query()->findOrFail($interview_template)
        );
    }

    public function store(InterviewTemplateStoreRequest $request): InterviewTemplateResource
    {
        $default_setting_values = InterviewTemplateSettingsDto::defaultValues();

        $interviewTemplate = (new CreateInterviewTemplateAction(
            InterviewTemplateDto::from(
                array_merge($request->validated(), [
                    'creator' => auth()->user(),
                    'owner' => $request->getOwnerInstance(),
                    'question_variants' => $request->questionVariants(),
                    'interview_template_settings_dto' => InterviewTemplateSettingsDto::from(
                        [
                            'started_at' => $request->date('settings.started_at', $default_setting_values->started_at,),
                            'ended_at'  => $request->date('settings.ended_at', $default_setting_values->ended_at),
                            'max_reconnection_tries' => $request->validated('settings.max_reconnection_tries', $default_setting_values->max_reconnection_tries)
                        ]
                    )
                ])
            )
        ))->execute()->load('questionVariants');

        return InterviewTemplateResource::make($interviewTemplate);
    }

    public function update()
    {
        //
    }

    public function destroy(int $interview_template)
    {
        return $interview_template;
    }
}
