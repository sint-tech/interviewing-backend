<?php

namespace App\Candidate\InterviewManagement\Controllers;

use App\Candidate\InterviewManagement\Queries\MyInterviewsQuery;
use App\Candidate\InterviewManagement\Resources\InterviewResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class MyInterviewsController extends Controller
{
    /**
     * @return AnonymousResourceCollection<InterviewResource>
     */
    public function __invoke(MyInterviewsQuery $query): AnonymousResourceCollection
    {
        return InterviewResource::collection(
            $query->paginate(
                pagination_per_page()
            )
        );
    }
}
