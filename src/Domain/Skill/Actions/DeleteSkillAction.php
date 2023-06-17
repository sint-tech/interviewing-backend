<?php

namespace Domain\Skill\Actions;

use Domain\Skill\Models\Skill;

class DeleteSkillAction
{
    public function __construct
    (
        public readonly int $skillId
    )
    {
    }

    public function execute():Skill
    {
        $skill = Skill::query()->findOrFail($this->skillId);

        $skill->delete();

        return $skill;
    }
}
