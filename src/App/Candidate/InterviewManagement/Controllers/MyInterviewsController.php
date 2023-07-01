<?php

namespace App\Candidate\InterviewManagement\Controllers;

use App\Candidate\InterviewManagement\Queries\MyInterviewsQuery;
use App\Candidate\InterviewManagement\Resources\InterviewResource;
use Support\Controllers\Controller;

class MyInterviewsController extends Controller
{
    public function __invoke(MyInterviewsQuery $query)
    {
        return InterviewResource::collection(
            $query->paginate(
                request()->integer('per_page', 25)
            )
        );
    }
}
