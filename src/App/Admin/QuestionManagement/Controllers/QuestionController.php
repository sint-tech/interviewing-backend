<?php

namespace App\Admin\QuestionManagement\Controllers;

use App\Admin\QuestionManagement\Factories\QuestionDataFactory;
use App\Admin\QuestionManagement\Queries\QuestionIndexQuery;
use App\Admin\QuestionManagement\Requests\QuestionStoreRequest;
use App\Admin\QuestionManagement\Requests\QuestionUpdateRequest;
use App\Admin\QuestionManagement\Resources\QuestionResource;
use Domain\QuestionManagement\Actions\CreateQuestionAction;
use Domain\QuestionManagement\Actions\DeleteQuestionAction;
use Domain\QuestionManagement\Actions\UpdateQuestionAction;
use Domain\QuestionManagement\Models\Question;
use Support\Controllers\Controller;

class QuestionController extends Controller
{
    public function index(QuestionIndexQuery $query)
    {
        return QuestionResource::collection($query->paginate(request()->integer('per_page', 25)));
    }

    public function show(int $question): QuestionResource
    {
        return QuestionResource::make(Question::query()->findOrFail($question));
    }

    public function store(QuestionStoreRequest $request): QuestionResource
    {
        $dto = QuestionDataFactory::fromRequest($request);

        return QuestionResource::make(
            (new CreateQuestionAction($dto))->execute()
        );
    }

    public function update(Question $question,QuestionUpdateRequest $request)
    {
        return QuestionResource::make(
          (new UpdateQuestionAction($question,QuestionDataFactory::fromUpdateRequest($request)))->execute()
        );
    }

    public function destroy(int $question): QuestionResource
    {
        $deletedQuestion = (new DeleteQuestionAction($question))->execute();

        return QuestionResource::make($deletedQuestion);
    }
}
