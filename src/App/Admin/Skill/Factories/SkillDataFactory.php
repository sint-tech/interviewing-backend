<?php

namespace App\Admin\Skill\Factories;

use App\Admin\Skill\Requests\SkillStoreRequest;
use App\Admin\Skill\Requests\SkillUpdateRequest;
use Domain\Skill\DataTransferObjects\SkillData;

class SkillDataFactory
{
    public static function fromRequest(SkillStoreRequest|SkillUpdateRequest $request): SkillData
    {
        return SkillData::from($request->validated());
    }
}
