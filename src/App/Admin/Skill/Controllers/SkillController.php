<?php

namespace App\Admin\Skill\Controllers;

use App\Admin\Skill\Factories\SkillDataFactory;
use App\Admin\Skill\Queries\IndexSkillQuery;
use App\Admin\Skill\Requests\SkillStoreRequest;
use App\Admin\Skill\Requests\SkillUpdateRequest;
use App\Admin\Skill\Resources\SkillResource;
use Domain\Skill\Actions\CreateSkillAction;
use Domain\Skill\Actions\DeleteSkillAction;
use Domain\Skill\Actions\UpdateSkillAction;
use Domain\Skill\Models\Skill;
use Support\Controllers\Controller;

class SkillController extends Controller
{
    public function index(IndexSkillQuery $query)
    {
        return SkillResource::collection($query->paginate((int) request()->input('per_page', 25)));
    }

    public function show(int $skill): SkillResource
    {
        return SkillResource::make(Skill::query()->findOrFail($skill));
    }

    public function store(
        SkillStoreRequest $request
    ) {
        $skill = (new CreateSkillAction(
            SkillDataFactory::fromRequest($request)
        ))->execute();

        return SkillResource::make($skill);
    }

    public function update(Skill $skill, SkillUpdateRequest $request)
    {
        return SkillResource::make(
            (new UpdateSkillAction($skill, SkillDataFactory::fromRequest($request)))->execute()
        );
    }

    public function destroy(int $skill)
    {
        return SkillResource::make(
            (new DeleteSkillAction($skill))->execute()
        );
    }
}
