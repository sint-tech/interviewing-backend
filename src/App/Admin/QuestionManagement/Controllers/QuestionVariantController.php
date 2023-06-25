<?php

namespace App\Admin\QuestionManagement\Controllers;

use App\Admin\QuestionManagement\Factories\QuestionVariantDataFactory;
use App\Admin\QuestionManagement\Queries\QuestionVariantIndexQuery;
use App\Admin\QuestionManagement\Requests\QuestionVariantStoreRequest;
use App\Admin\QuestionManagement\Resources\QuestionResource;
use App\Admin\QuestionManagement\Resources\QuestionVariantResource;
use Domain\QuestionManagement\Actions\CreateQuestionVariantAction;
use Domain\QuestionManagement\Actions\DeleteQuestionVariantAction;
use Domain\QuestionManagement\Models\QuestionVariant;
use Support\Controllers\Controller;

class QuestionVariantController extends Controller
{
    public function index(QuestionVariantIndexQuery $query)
    {
        return QuestionVariantResource::collection(
            $query->paginate(
                request()->integer('per_page', 25)
            )
        );
    }

    public function show(int $question_variant)
    {
        return QuestionResource::make(QuestionVariant::query()->findOrFail($question_variant));
    }

    public function store(QuestionVariantStoreRequest $request)
    {
        $question_variant_dto = QuestionVariantDataFactory::fromRequest($request);

        return QuestionVariantResource::make(
            (new CreateQuestionVariantAction($question_variant_dto))->execute()
        );
    }

    public function update()
    {
        //
    }

    public function destroy(int $question_variant): QuestionVariantResource
    {
        return QuestionVariantResource::make(
            (new DeleteQuestionVariantAction($question_variant))->execute()
        );
    }
}
