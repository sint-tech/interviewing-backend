<?php

namespace App\Admin\QuestionManagement\Controllers;

use App\Admin\QuestionManagement\Factories\QuestionVariantDataFactory;
use App\Admin\QuestionManagement\Queries\QuestionVariantIndexQuery;
use App\Admin\QuestionManagement\Requests\QuestionVariantStoreRequest;
use App\Admin\QuestionManagement\Requests\QuestionVariantUpdateRequest;
use App\Admin\QuestionManagement\Resources\QuestionVariantResource;
use Domain\QuestionManagement\Actions\CreateQuestionVariantAction;
use Domain\QuestionManagement\Actions\DeleteQuestionVariantAction;
use Domain\QuestionManagement\Actions\UpdateQuestionVariantAction;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class QuestionVariantController extends Controller
{
    public function index(QuestionVariantIndexQuery $query): AnonymousResourceCollection
    {
        return QuestionVariantResource::collection(
            $query->paginate(
                pagination_per_page()
            )
        );
    }

    public function show(int $question_variant): QuestionVariantResource
    {
        return QuestionVariantResource::make(QuestionVariant::query()->findOrFail($question_variant));
    }

    public function store(QuestionVariantStoreRequest $request, CreateQuestionVariantAction $action): QuestionVariantResource
    {
        $question_variant_dto = QuestionVariantDataFactory::fromRequest($request);

        return QuestionVariantResource::make(
            $action->execute($question_variant_dto)->load(['organization', 'aiPrompts'])
        );
    }

    public function update(QuestionVariant $questionVariant, QuestionVariantUpdateRequest $request, UpdateQuestionVariantAction $action): QuestionVariantResource
    {
        return QuestionVariantResource::make(
            $action->execute($questionVariant, QuestionVariantDataFactory::fromUpdateRequest($request))
        );
    }

    public function destroy(int $question_variant): QuestionVariantResource
    {
        return QuestionVariantResource::make(
            (new DeleteQuestionVariantAction($question_variant))->execute()
        );
    }
}
