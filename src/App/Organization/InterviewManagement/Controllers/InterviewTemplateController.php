<?php

namespace App\Organization\InterviewManagement\Controllers;

use App\Organization\InterviewManagement\Requests\InterviewTemplateStoreRequest;
use App\Organization\InterviewManagement\Resources\InterviewTemplateResource;
use Domain\InterviewManagement\Actions\CreateInterviewTemplateAction;
use Domain\InterviewManagement\DataTransferObjects\InterviewTemplateDto;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class InterviewTemplateController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return InterviewTemplateResource::collection(
            InterviewTemplate::query()->paginate(pagination_per_page())
        );
    }

    public function show(int $interview_template): InterviewTemplateResource
    {
        return InterviewTemplateResource::make(
            InterviewTemplate::query()->findOrFail($interview_template)
        );
    }

    public function store(InterviewTemplateStoreRequest $request, CreateInterviewTemplateAction $action): InterviewTemplateResource
    {
        $dto = InterviewTemplateDto::from(array_merge($request->validated(), [
            'owner' => auth()->user()->organization,
            'creator' => auth()->user(),
            'question_variants' => QuestionVariant::query()->whereIntegerInRaw('id', $request->validated('question_variants'))->get(),
        ]));

        return InterviewTemplateResource::make(
            $action->execute($dto)
        );
    }

    public function update()
    {
        //
    }

    public function destroy(int $interview_template): InterviewTemplateResource
    {
        $interviewTemplate = InterviewTemplate::query()->findOrFail($interview_template);

        $interviewTemplate->delete();

        return InterviewTemplateResource::make(
            $interviewTemplate
        );
    }
}
