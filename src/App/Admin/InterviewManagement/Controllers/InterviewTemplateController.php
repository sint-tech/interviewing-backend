<?php

namespace App\Admin\InterviewManagement\Controllers;

use App\Admin\InterviewManagement\Factories\InterviewTemplateDataFactory;
use App\Admin\InterviewManagement\Requests\InterviewTemplateUpdateRequest;
use App\Admin\InterviewManagement\Requests\InterviewTemplateStoreRequest;
use App\Admin\InterviewManagement\Resources\InterviewTemplateResource;
use Domain\InterviewManagement\Actions\CreateInterviewTemplateAction;
use Domain\InterviewManagement\Actions\UpdateInterviewTemplateAction;
use Domain\InterviewManagement\DataTransferObjects\InterviewTemplateDto;
use Domain\InterviewManagement\DataTransferObjects\InterviewTemplateSettingsDto;
use Domain\InterviewManagement\Models\Interview;
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

    public function store(InterviewTemplateStoreRequest $request,InterviewTemplateDataFactory $interviewTemplateDataFactory): InterviewTemplateResource
    {
        $interviewTemplate = (new CreateInterviewTemplateAction(
            $interviewTemplateDataFactory->fromRequest($request))
        )->execute()->load('questionVariants');

        return InterviewTemplateResource::make($interviewTemplate);
    }

    public function update(
        InterviewTemplate $interview_template,
        InterviewTemplateUpdateRequest $request,
        UpdateInterviewTemplateAction $updateInterviewTemplateAction
    ):InterviewTemplateResource
    {
        $dto = (new InterviewTemplateDataFactory)->fromRequest($request);

        return InterviewTemplateResource::make(
            $updateInterviewTemplateAction->execute($interview_template,$dto)
        );
    }

    public function destroy(int $interview_template)
    {
        return $interview_template;
    }
}
