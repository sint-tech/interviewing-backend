<?php

namespace App\Organization\InterviewManagement\Controllers;

use App\Organization\InterviewManagement\Requests\InterviewTemplateStoreRequest;
use App\Organization\InterviewManagement\Requests\InterviewTemplateUpdateRequest;
use App\Organization\InterviewManagement\Resources\InterviewTemplateResource;
use Domain\InterviewManagement\Actions\CreateInterviewTemplateAction;
use Domain\InterviewManagement\Actions\UpdateInterviewTemplateAction;
use Domain\InterviewManagement\DataTransferObjects\InterviewTemplateDto;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;
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
            QueryBuilder::for(InterviewTemplate::class)
                ->allowedIncludes([
                    AllowedInclude::relationship('questionVariants'),
                    AllowedInclude::relationship('questionClusters'),
                    AllowedInclude::relationship('questionClusters.skills'),
                    AllowedInclude::callback('questionClusters.questions', function ($query) use ($interview_template) {
                        $query->withWhereHas('questionVariants', function ($query) use ($interview_template) {
                            $query->whereInterviewTemplate($interview_template);
                        });
                    }),
                ])
                ->findOrFail($interview_template)
        );
    }

    public function store(InterviewTemplateStoreRequest $request, CreateInterviewTemplateAction $action): InterviewTemplateResource
    {
        $questionVariantIds = $request->validated('question_variants');
        $questionVariants = QuestionVariant::query()->whereIn('id', $questionVariantIds)->get();

        $questionVariants = $questionVariants->sortBy(function ($questionVariant) use ($questionVariantIds) {
            return array_search($questionVariant->id, $questionVariantIds);
        });

        $dto = InterviewTemplateDto::from(array_merge($request->validated(), [
            'organization_id' => auth()->user()->organization_id,
            'creator' => auth()->user(),
            'question_variants' => $questionVariants->values(),
        ]));

        return InterviewTemplateResource::make(
            $action->execute($dto)->load(['questionVariants'])
        );
    }

    public function update(InterviewTemplate $interview_template, InterviewTemplateUpdateRequest $request, UpdateInterviewTemplateAction $action): InterviewTemplateResource
    {
        $data = $interview_template->attributesToArray() +
            ['creator' => $interview_template->creator] +
            ['question_variants' => $interview_template->questionVariants];

        $updatedData = $request->validated();
        if ($request->filled('question_variants')) {
            $questionVariantIds = $request->input('question_variants');
            $questionVariants = QuestionVariant::query()->whereIn('id', $questionVariantIds)->get();

            $questionVariants = $questionVariants->sortBy(function ($questionVariant) use ($questionVariantIds) {
                return array_search($questionVariant->id, $questionVariantIds);
            });

            $updatedData['question_variants'] = $questionVariants->values();
        }

        $dto = InterviewTemplateDto::from(array_merge(
            $data,
            $updatedData
        ));

        return InterviewTemplateResource::make(
            $action->execute($interview_template, $dto)
        );
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
