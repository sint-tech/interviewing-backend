<?php

namespace App\Admin\JobTitle\Controllers;

use App\Admin\JobTitle\Queries\IndexJobTitleQuery;
use App\Admin\JobTitle\Requests\JobTitleStoreRequest;
use App\Admin\JobTitle\Requests\JobTitleUpdateRequest;
use App\Admin\JobTitle\Resources\JobTitleResource;
use Domain\JobTitle\Actions\CreateJobTitleAction;
use Domain\JobTitle\Actions\UpdateJobTitleAction;
use Domain\JobTitle\DataTransferObjects\JobTitleDto;
use Domain\JobTitle\Models\JobTitle;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class JobTitleController extends Controller
{
    /**
     * @return AnonymousResourceCollection<JobTitleResource>
     */
    public function index(IndexJobTitleQuery $query): AnonymousResourceCollection
    {
        return JobTitleResource::collection(
            $query->paginate(pagination_per_page())
        );
    }

    public function show(int $job_title): JobTitleResource
    {
        return JobTitleResource::make(JobTitle::query()->findOrFail($job_title));
    }

    public function store(JobTitleStoreRequest $request, CreateJobTitleAction $action): JobTitleResource
    {
        return JobTitleResource::make(
            $action->execute(JobTitleDto::from($request->validated()))
        );
    }

    public function update(JobTitleUpdateRequest $request, int $job_title, UpdateJobTitleAction $action)
    {
        $dto = JobTitleDto::from(
            array_merge(
                ($job_title = JobTitle::query()->findOrFail($job_title))->toArray(),
                $request->validated()
            )
        );

        return JobTitleResource::make($action->execute($job_title, $dto));
    }

    public function destroy(int $job_title)
    {
        abort(404);
    }
}
