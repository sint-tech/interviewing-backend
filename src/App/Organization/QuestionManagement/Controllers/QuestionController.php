<?php

namespace App\Organization\QuestionManagement\Controllers;

use App\Organization\QuestionManagement\Resources\QuestionResource;
use Domain\QuestionManagement\Models\Question;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class QuestionController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return QuestionResource::collection(
            Question::query()->whereHas('defaultAIPrompt')->paginate(pagination_per_page())
        );
    }

    public function show(Question $question): QuestionResource
    {
        return QuestionResource::make($question);
    }
}
