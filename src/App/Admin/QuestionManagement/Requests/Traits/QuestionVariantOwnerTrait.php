<?php

namespace App\Admin\QuestionManagement\Requests\Traits;

use Domain\Organization\Models\Organization;
use Domain\QuestionManagement\Enums\QuestionVariantOwnerEnum;
use Domain\Users\Models\User;

/** @deprecated  */
trait QuestionVariantOwnerTrait
{
    final public function getOwnerInstance(): Organization|User|string
    {
        return match ($this->validated('owner.model_type')) {
            QuestionVariantOwnerEnum::Admin->value => User::query()->find($this->validated('owner.model_id')),
            QuestionVariantOwnerEnum::Organization->value => Organization::query()->find($this->validated('owner.model_id')),
        };
    }
}
