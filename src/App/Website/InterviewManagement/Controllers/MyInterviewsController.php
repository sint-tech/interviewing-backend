<?php

namespace App\Website\InterviewManagement\Controllers;

use App\Website\InterviewManagement\Queries\MyInterviewsQuery;
use App\Website\InterviewManagement\Resources\InterviewResource;
use Support\Controllers\Controller;

class MyInterviewsController extends Controller
{
    public function __invoke(MyInterviewsQuery $query)
    {
        return InterviewResource::collection(
            $query->paginate(
                request()->integer('per_page',25)
            )
        );
    }
}
