<?php

namespace App\Admin\AnswerManagement\Controllers;

use App\Admin\AnswerManagement\Queries\AnswerVariantIndexQuery;
use App\Admin\AnswerManagement\Resources\AnswerVariantResource;
use Domain\AnswerManagement\Models\AnswerVariant;
use Support\Controllers\Controller;

class AnswerVariantController extends Controller
{
    public function index(AnswerVariantIndexQuery $query)
    {
        return AnswerVariantResource::collection(
            $query->paginate(
                request()->integer('per_page')
            )
        );
    }

    public function show(int $answer_variant_id)
    {
        return AnswerVariantResource::make(
            AnswerVariant::query()->firstOrFail($answer_variant_id)
        );
    }

    public function store()
    {
        //
    }

    public function update()
    {
        //
    }

    public function destroy()
    {
        //
    }
}
