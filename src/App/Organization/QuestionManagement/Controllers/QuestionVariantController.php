<?php

namespace App\Organization\QuestionManagement\Controllers;

use App\Organization\QuestionManagement\Queries\QuestionVariantIndexQuery;
use App\Organization\QuestionManagement\Requests\QuestionVariantStoreRequest;
use App\Organization\QuestionManagement\Requests\QuestionVariantUpdateRequest;
use App\Organization\QuestionManagement\Resources\QuestionVariantResource;
use Domain\QuestionManagement\Actions\CreateQuestionVariantAction;
use Domain\QuestionManagement\Actions\UpdateQuestionVariantAction;
use Domain\QuestionManagement\DataTransferObjects\QuestionVariantDto;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class QuestionVariantController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(QuestionVariant::class, 'question_variant');
    }
    /**
     * @return AnonymousResourceCollection<QuestionVariantResource>
     */
    public function index(QuestionVariantIndexQuery $query): AnonymousResourceCollection
    {
        return QuestionVariantResource::collection(
            $query->paginate(pagination_per_page())
        );
    }

    public function show(QuestionVariant $question_variant): QuestionVariantResource
    {
        return QuestionVariantResource::make(
            $question_variant
        );
    }

    public function store(QuestionVariantStoreRequest $request, CreateQuestionVariantAction $action): QuestionVariantResource
    {
        $dto = QuestionVariantDto::from(
            $request->validated() + [
                'organization_id' => auth()->user()->organization_id,
                'creator' => auth()->user(),
                'ai_prompts' => [$request->question()->defaultAIPrompt->toArray()],
            ]
        );

        return QuestionVariantResource::make(
            $action->execute($dto)
        );
    }

    public function update(QuestionVariantUpdateRequest $request, QuestionVariant $question_variant, UpdateQuestionVariantAction $action): QuestionVariantResource
    {
        $data = QuestionVariantDto::from(
            array_merge($question_variant->attributesToArray(), $request->validated() + [
                'ai_prompts' => [$request->question()->defaultAIPrompt->toArray()],
            ])
        );

        return QuestionVariantResource::make($action->execute($question_variant, $data));
    }

    public function destroy(QuestionVariant $question_variant): QuestionVariantResource
    {
        $question_variant->delete();

        return QuestionVariantResource::make($question_variant);
    }
}
