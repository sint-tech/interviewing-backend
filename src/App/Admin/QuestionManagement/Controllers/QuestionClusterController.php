<?php

namespace App\Admin\QuestionManagement\Controllers;

use App\Admin\QuestionManagement\Factories\QuestionClusterDataFactory;
use App\Admin\QuestionManagement\Queries\QuestionClusterIndexQuery;
use App\Admin\QuestionManagement\Requests\QuestionClusterStoreRequest;
use App\Admin\QuestionManagement\Requests\QuestionClusterUpdateRequest;
use App\Admin\QuestionManagement\Resources\QuestionClusterResource;
use Domain\QuestionManagement\Actions\CreateQuestionClusterAction;
use Domain\QuestionManagement\Actions\DeleteQuestionClusterAction;
use Domain\QuestionManagement\Actions\UpdateQuestionClusterAction;
use Domain\QuestionManagement\Models\QuestionCluster;
use Support\Controllers\Controller;

class QuestionClusterController extends Controller
{
    public function index(QuestionClusterIndexQuery $query)
    {
        return QuestionClusterResource::collection($query->paginate(request()->integer('per_page', 25)));
    }

    public function show(int $questionCluster): QuestionClusterResource
    {
        return QuestionClusterResource::make(QuestionCluster::query()->findOrFail($questionCluster));
    }

    public function store(QuestionClusterStoreRequest $request)
    {
        $dto = QuestionClusterDataFactory::fromRequest($request);

        $questionCluster = (new CreateQuestionClusterAction($dto))->execute();

        return QuestionClusterResource::make($questionCluster);
    }

    public function update(QuestionCluster $questionCluster,QuestionClusterUpdateRequest $request)
    {
        $question_cluster = (new UpdateQuestionClusterAction($questionCluster,QuestionClusterDataFactory::fromRequest($request)))->execute();

        return QuestionClusterResource::make($question_cluster);
    }

    public function destroy(int $questionCluster): QuestionClusterResource
    {
        return QuestionClusterResource::make((new DeleteQuestionClusterAction($questionCluster))->execute());
    }
}
