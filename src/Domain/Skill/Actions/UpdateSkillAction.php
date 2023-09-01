<?php

namespace Domain\Skill\Actions;

use Domain\Skill\DataTransferObjects\SkillData;
use Domain\Skill\Models\Skill;

class UpdateSkillAction
{
    public function __construct(
        public Skill $skill,
        public readonly SkillData $skillData,
    )
    {}

    public function execute():Skill
    {
        $this->skill->update($this->skillData->toArray());

        return $this->skill->refresh();
    }
}
