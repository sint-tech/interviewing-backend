<?php

namespace App\Admin\InterviewManagement\Controllers;

use App\Admin\InterviewManagement\Factories\InterviewTemplateDataFactory;
use App\Admin\InterviewManagement\Queries\InterviewTemplateIndexQuery;
use App\Admin\InterviewManagement\Requests\InterviewTemplateStoreRequest;
use App\Admin\InterviewManagement\Requests\InterviewTemplateUpdateRequest;
use App\Admin\InterviewManagement\Resources\InterviewTemplateResource;
use Domain\InterviewManagement\Actions\CreateInterviewTemplateAction;
use Domain\InterviewManagement\Actions\UpdateInterviewTemplateAction;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Support\Controllers\Controller;

class InterviewTemplateController extends Controller
{
    public function index(InterviewTemplateIndexQuery $query)
    {
        return InterviewTemplateResource::collection(
            $query->paginate(
                request()->integer('per_page', 30)
            )
        );
    }

    public function show(int $interview_template): InterviewTemplateResource
    {
        return InterviewTemplateResource::make(
            InterviewTemplate::query()->findOrFail($interview_template)->load('questionVariants')
        );
    }

    public function store(InterviewTemplateStoreRequest $request, InterviewTemplateDataFactory $interviewTemplateDataFactory): InterviewTemplateResource
    {
        $interviewTemplate = (new CreateInterviewTemplateAction()
        )->execute(
            $interviewTemplateDataFactory->fromRequest($request)
        )->load('questionVariants');

        return InterviewTemplateResource::make($interviewTemplate);
    }

    public function update(
        InterviewTemplate $interview_template,
        InterviewTemplateUpdateRequest $request,
        UpdateInterviewTemplateAction $updateInterviewTemplateAction
    ): InterviewTemplateResource {
        $dto = (new InterviewTemplateDataFactory)->fromRequest($request);

        return InterviewTemplateResource::make(
            $updateInterviewTemplateAction->execute($interview_template, $dto)
        );
    }

    public function destroy(int $interview_template): InterviewTemplateResource
    {
        $interview_template = InterviewTemplate::query()->findOrFail($interview_template);

        $interview_template->questionVariants()->detach();

        $interview_template->delete();

        return InterviewTemplateResource::make($interview_template);
    }
}
