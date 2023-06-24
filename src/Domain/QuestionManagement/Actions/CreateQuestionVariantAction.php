<?php

namespace Domain\QuestionManagement\Actions;

use Domain\Organization\Models\Organization;
use Domain\QuestionManagement\DataTransferObjects\QuestionVariantDto;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\Users\Models\User;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Support\Arr;
use Illuminate\Support\Optional;

class CreateQuestionVariantAction
{
    public function __construct(
        public QuestionVariantDto $questionVariantDto
    )
    {
    }

    public function execute():QuestionVariant
    {
        $question_variant = new QuestionVariant();

        $data = $this->questionVariantDto->toArray();

        $data = array_merge($data,[
            'creator_type'  => $this->questionVariantDto->creator::class,
            'creator_id'  => $this->questionVariantDto->creator->getKey(),
            'owner_type'  => $this->questionVariantDto->owner::class,
            'owner_id'  => $this->questionVariantDto->owner->getKey(),
        ]);

        $question_variant->fill($data)->save();

        return $question_variant->refresh()->load([
            'question',
            'owner',
            'creator'
        ]);
    }
}
