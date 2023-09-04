<?php

namespace App\Admin\AIModelManagement\Controllers;

use App\Admin\AIModelManagement\Queries\IndexAIModelQuery;
use App\Admin\AIModelManagement\Resources\AIModelResource;
use Domain\AiPromptMessageManagement\Models\AIModel;
use Support\Controllers\Controller;

class AIModelController extends Controller
{
    public function index(IndexAIModelQuery $query)
    {
        return AIModelResource::collection($query->paginate(request()->integer('per_page',25)));
    }

    public function show(AIModel $ai_model)
    {
        return AIModelResource::make(
            $ai_model
        );
    }
}
