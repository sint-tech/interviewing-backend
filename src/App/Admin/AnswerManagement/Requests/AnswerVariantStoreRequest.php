<?php

namespace App\Admin\AnswerManagement\Requests;

use Domain\AnswerManagement\Models\Answer;
use Domain\Organization\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnswerVariantStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'answer_id' => ['required', Rule::exists('answers', 'id')->withoutTrashed()],
            'text' => ['required', 'string', 'min:3', 'max:100000'],
            'description' => ['nullable', 'string', 'min:3', 'max:1000'],
            'score' => ['required', 'numeric', 'between:'.implode(',', $this->allowedScoreRange())],
            'organization_id' => ['nullable', Rule::exists(Organization::class, 'id')->withoutTrashed()],
        ];
    }

    protected function allowedScoreRange(): array
    {
        $answer = Answer::query()->find($this->input('answer_id'));

        $min = $answer?->min_score ?: 1;
        $max = $answer?->max_score ?: 10;

        return [$min, $max];
    }
}
