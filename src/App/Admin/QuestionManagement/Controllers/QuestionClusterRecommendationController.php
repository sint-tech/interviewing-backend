<?php

namespace App\Admin\QuestionManagement\Controllers;

use App\Admin\QuestionManagement\Requests\QuestionClusterRecommendationStoreRequest;
use App\Admin\QuestionManagement\Resources\QuestionClusterRecommendationResource;
use Domain\QuestionManagement\Models\QuestionClusterRecommendation;
use Support\Controllers\Controller;

class QuestionClusterRecommendationController extends Controller
{
    public function index()
    {
        //
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
