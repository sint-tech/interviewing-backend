<?php

namespace App\Organization\QuestionManagement\Controllers;

use App\Organization\QuestionManagement\Requests\QuestionVariantStoreRequest;
use App\Organization\QuestionManagement\Resources\QuestionVariantResource;
use Domain\QuestionManagement\Actions\CreateQuestionVariantAction;
use Domain\QuestionManagement\DataTransferObjects\QuestionVariantDto;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class QuestionVariantController extends Controller
{
    /**
     * @return AnonymousResourceCollection<QuestionVariantResource>
     */
    public function index(): AnonymousResourceCollection
    {
        return QuestionVariantResource::collection(
            QuestionVariant::query()->paginate(pagination_per_page())
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

    public function update()
    {
        //
    }

    public function destroy(int $question_variant): QuestionVariantResource
    {
        $question_variant = QuestionVariant::query()->findOrFail($question_variant);

        $question_variant->delete();

        return QuestionVariantResource::make($question_variant);
    }
}
