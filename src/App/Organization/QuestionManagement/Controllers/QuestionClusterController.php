<?php

namespace App\Organization\QuestionManagement\Controllers;

use App\Organization\QuestionManagement\Resources\QuestionClusterResource;
use Domain\QuestionManagement\Models\QuestionCluster;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;
use Support\Controllers\Controller;

class QuestionClusterController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return QuestionClusterResource::collection(
            QueryBuilder::for(QuestionCluster::query())
                ->allowedFilters(['skills.id'])
                ->allowedIncludes('skills', 'questionVariants', 'questions')
                ->paginate(pagination_per_page())
        );
    }

    public function show(QuestionCluster $questionCluster): QuestionClusterResource
    {
        return QuestionClusterResource::make($questionCluster);
    }
}
