<?php

namespace App\Admin\QuestionManagement\Requests;

use Domain\Candidate\Models\Candidate;
use Domain\Organization\Models\Organization;
use Domain\QuestionManagement\Enums\QuestionVariantOwnerEnum;
use Domain\Users\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

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
            'owner_type' => ['required', 'string', new Enum(QuestionVariantOwnerEnum::class)],
            'owner_id' => [
                'required',
                Rule::when(
                    $this->getOwnerTableName(),
                    [Rule::exists($this->getOwnerTableName(), 'id')]
                ),
            ],
        ];
    }

    protected function getOwnerTableName(): string
    {
        return match ($this->input('owner_type')) {
            QuestionVariantOwnerEnum::Admin->value => 'users',
            QuestionVariantOwnerEnum::Candidate->value => 'candidates',
            default => '',
        };
    }

    final public function getOwnerInstance(): Organization|User|string
    {
        return match ($this->validated('owner_type')) {
            QuestionVariantOwnerEnum::Admin->value => User::query()->find($this->validated('owner_id')),
            QuestionVariantOwnerEnum::Candidate->value => Candidate::query()->find($this->validated('owner_id')),
        };
    }
}
