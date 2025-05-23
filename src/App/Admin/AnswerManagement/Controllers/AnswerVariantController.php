<?php

namespace App\Admin\AnswerManagement\Controllers;

use App\Admin\AnswerManagement\Factories\AnswerVariantDataFactory;
use App\Admin\AnswerManagement\Queries\AnswerVariantIndexQuery;
use App\Admin\AnswerManagement\Requests\AnswerVariantStoreRequest;
use App\Admin\AnswerManagement\Resources\AnswerVariantResource;
use Domain\AnswerManagement\Actions\CreateAnswerVariantAction;
use Domain\AnswerManagement\Models\AnswerVariant;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class AnswerVariantController extends Controller
{
    public function index(AnswerVariantIndexQuery $query): AnonymousResourceCollection
    {
        return AnswerVariantResource::collection(
            $query->paginate(
                request()->integer('per_page')
            )
        );
    }

    public function show(int $answer_variant_id): AnswerVariantResource
    {
        return AnswerVariantResource::make(
            AnswerVariant::query()
                ->firstOrFail($answer_variant_id)
                ->load(['organization'])
        );
    }

    public function store(AnswerVariantStoreRequest $request): AnswerVariantResource
    {
        $answer_variant_dto = AnswerVariantDataFactory::fromStoreRequest($request);

        return AnswerVariantResource::make(
            (new CreateAnswerVariantAction($answer_variant_dto))->execute()->load(['organization'])
        );
    }

    public function update()
    {
        //
    }

    public function destroy(int $answer_variant_id)
    {
        //todo delete answer variant
    }
}
