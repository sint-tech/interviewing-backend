<?php

namespace App\Organization\JobTitle\Controllers;

use App\Organization\JobTitle\Resources\JobTitleResource;
use Domain\JobTitle\Models\JobTitle;
use Support\Controllers\Controller;

class JobTitleController extends Controller
{
    public function index()
    {
        return JobTitleResource::collection(
            JobTitle::query()->paginate(pagination_per_page())
        );
    }

    public function show(JobTitle $job_title)
    {
        return JobTitleResource::make($job_title);
    }
}
