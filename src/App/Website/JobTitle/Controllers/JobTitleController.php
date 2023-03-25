<?php

namespace App\Website\JobTitle\Controllers;

use App\Website\JobTitle\Queries\JobTitleIndexQuery;
use App\Website\JobTitle\Resources\JobTitleResource;
use Domain\JobTitle\Models\JobTitle;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class JobTitleController extends Controller
{
    /**
     * @param JobTitleIndexQuery $indexQuery
     * @return AnonymousResourceCollection
     */
    public function index(JobTitleIndexQuery $indexQuery):AnonymousResourceCollection
    {
        $jobTitles = $indexQuery->paginate(request()->input("per_page",25));

        return JobTitleResource::collection($jobTitles);
    }

    /**
     * @param int $jobTitleId
     * @return JobTitleResource
     */
    public function show(int $jobTitleId):JobTitleResource
    {
        $jobTitle = JobTitle::query()->findOrFail($jobTitleId);

        return JobTitleResource::make($jobTitle);
    }
}
