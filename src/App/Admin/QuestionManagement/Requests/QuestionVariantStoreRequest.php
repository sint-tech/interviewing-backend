<?php

namespace App\Admin\QuestionManagement\Requests;

use Domain\Organization\Models\Organization;
use Domain\QuestionManagement\Enums\QuestionVariantOwnerEnum;
use Domain\Users\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Support\Rules\MorphRelationExistRule;

class QuestionVariantStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'min:3', 'max:1000'],
            'description' => ['string', 'min:3', 'max:1000'],
            'question_id' => ['required', Rule::exists('questions', 'id')->withoutTrashed()],
            'reading_time_in_seconds' => ['required', 'integer', 'min:1'],
            'answering_time_in_seconds' => ['required', 'integer', 'min:1'],
            'owner'     => ['required',
                'array',
                (new MorphRelationExistRule(
                    QuestionVariantOwnerEnum::class,
                    $this->input('owner.model_type') == 'admin' ? 'users' : null)
                )
            ],
        ];
    }

    final public function getOwnerInstance(): Organization|User|string
    {
        return match ($this->validated('owner.model_type')) {
            QuestionVariantOwnerEnum::Admin->value => User::query()->find($this->validated('owner.model_id')),
            QuestionVariantOwnerEnum::Organization->value => Organization::query()->find($this->validated('owner.model_id')),
        };
    }
}
