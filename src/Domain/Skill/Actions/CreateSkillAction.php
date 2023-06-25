<?php

namespace Domain\Skill\Actions;

use Domain\Skill\DataTransferObjects\SkillData;
use Domain\Skill\Models\Skill;

class CreateSkillAction
{
    public function __construct(
        protected readonly SkillData $skillData
    ) {

    }

    public function execute(): Skill
    {
        return Skill::query()->create($this->skillData->toArray());
    }
}
