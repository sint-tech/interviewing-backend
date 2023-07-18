<?php

namespace App\Admin\InterviewManagement\Controllers;

use App\Admin\InterviewManagement\Requests\InterviewTemplateStoreRequest;
use App\Admin\InterviewManagement\Resources\InterviewTemplateResource;
use Domain\InterviewManagement\Actions\CreateInterviewTemplateAction;
use Domain\InterviewManagement\DataTransferObjects\InterviewTemplateDto;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Support\Controllers\Controller;
use Support\Interfaces\OwnerInterface;

class InterviewTemplateController extends Controller
{
    public function index()
    {
        return InterviewTemplateResource::collection(
            InterviewTemplate::query()->paginate(
                request()->integer('per_page',25)
            )
        );
    }

    public function show(int $interview_template):InterviewTemplateResource
    {
        return InterviewTemplateResource::make(
            InterviewTemplate::query()->findOrFail($interview_template)
        );
    }

    public function store(InterviewTemplateStoreRequest $request):InterviewTemplateResource
    {
        $interviewTemplate = (new CreateInterviewTemplateAction(
            InterviewTemplateDto::from(
                array_merge($request->validated(),[
                    'creator' => auth()->user(),
                    'owner' => $request->getOwnerInstance(),
                    'question_variants' => $request->questionVariants()
                ])
            )
        ))->execute();

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
