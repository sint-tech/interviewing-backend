<?php

namespace App\Admin\InterviewManagement\Requests;

use Domain\InterviewManagement\Enums\InterviewTemplateAvailabilityStatusEnum;
use Domain\Organization\Models\Organization;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\Users\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Support\Interfaces\OwnerEnum;
use Support\Interfaces\OwnerInterface;
use Support\Rules\MorphRelationExistRule;

class InterviewTemplateStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'min:3', 'max:1000'],
            'availability_status' => ['required', Rule::enum(InterviewTemplateAvailabilityStatusEnum::class)],
            'owner' => ['required', 'array', new MorphRelationExistRule(
                OwnerEnum::class,
                $this->input('owner.model_type') == OwnerEnum::Admin->value ? 'users' : null,
            )],
            'reusable' => ['sometimes', 'boolean'],
            'question_variant_ids' => ['required', 'array'],
            'question_variant_ids.*' => ['required', 'numeric', Rule::exists('question_variants', 'id')->withoutTrashed(), 'distinct'],
        ];
    }

    final public function getOwnerInstance(): OwnerInterface
    {
        return match ($this->enum('owner.model_type', OwnerEnum::class)) {
            OwnerEnum::Admin => User::query()->find($this->validated('owner.model_id')),
            OwnerEnum::Organization => Organization::query()->find($this->validated('owner.model_id'))
        };
    }

    public function questionVariants(): Collection
    {
        return QuestionVariant::query()
            ->whereKey($this->validated('question_variant_ids'))
            ->with('questionCluster')
            ->get();
    }
}
