<?php

namespace App\Admin\QuestionManagement\Controllers;

use App\Admin\QuestionManagement\Queries\QuestionClusterRecommendationQuery;
use App\Admin\QuestionManagement\Requests\QuestionClusterRecommendationStoreRequest;
use App\Admin\QuestionManagement\Resources\QuestionClusterRecommendationResource;
use Domain\QuestionManagement\Models\QuestionClusterRecommendation;
use Support\Controllers\Controller;

class QuestionClusterRecommendationController extends Controller
{
    public function index(QuestionClusterRecommendationQuery $query)
    {
        return QuestionClusterRecommendationResource::collection(
            $query->paginate(request()->integer('per_page', 25))
        );
    }

    public function show()
    {
        //
    }

    public function store(QuestionClusterRecommendationStoreRequest $request)
    {
        return QuestionClusterRecommendationResource::make(
            QuestionClusterRecommendation::query()->create(
                $request->validated()
            )
        );
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
