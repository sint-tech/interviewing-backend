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
                request()->integer('per_page', 25)
            )
        );
    }

    public function show(int $question_variant): QuestionVariantResource
    {
        return QuestionVariantResource::make(QuestionVariant::query()->findOrFail($question_variant));
    }

    public function store(QuestionVariantStoreRequest $request): QuestionVariantResource
    {
        $question_variant_dto = QuestionVariantDataFactory::fromRequest($request);

        return QuestionVariantResource::make(
            (new CreateQuestionVariantAction())->execute($question_variant_dto)->load('organization')
        );
    }

    public function update(QuestionVariant $questionVariant, QuestionVariantUpdateRequest $request): QuestionVariantResource
    {
        return QuestionVariantResource::make(
            (new UpdateQuestionVariantAction())->execute(
                $questionVariant, QuestionVariantDataFactory::fromUpdateRequest($request)
            )
        );
    }

    public function destroy(int $question_variant): QuestionVariantResource
    {
        return QuestionVariantResource::make(
            (new DeleteQuestionVariantAction($question_variant))->execute()
        );
    }
}
