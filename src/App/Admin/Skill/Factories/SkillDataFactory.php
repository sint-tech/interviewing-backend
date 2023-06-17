<?php

namespace App\Admin\Skill\Factories;

use App\Admin\Skill\Requests\SkillStoreRequest;
use Domain\Skill\DataTransferObjects\SkillData;

class SkillDataFactory
{
    public static function fromRequest(SkillStoreRequest $request):SkillData
    {
        return SkillData::from($request->validated());
    }
}
