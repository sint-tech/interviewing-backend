<?php

namespace App\Organization\SkillManagement\Controllers;

use App\Organization\SkillManagement\Resources\SkillResource;
use Domain\Skill\Models\Skill;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class SkillController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return SkillResource::collection(
            Skill::query()->paginate(pagination_per_page())
        );
    }

    public function show(Skill $skill): SkillResource
    {
        return SkillResource::make($skill);
    }
}
