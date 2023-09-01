<?php

namespace App\Admin\QuestionManagement\Requests;

use Domain\QuestionManagement\Enums\QuestionTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class QuestionUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['filled', 'string', 'min:1', 'max:255'],
            'description' => ['nullable', 'string', 'min:1', 'max:1000'],
            'question_cluster_id' => ['filled', Rule::exists('question_clusters', 'id')->withoutTrashed()],
            'difficult_level' => ['filled', 'integer', 'min:1', 'max:10'],
            'question_type' => ['filled', (new Enum(QuestionTypeEnum::class))],
            'min_reading_duration_in_seconds' => ['filled', 'integer', 'min:1', 'lt:max_reading_duration_in_seconds'],
            'max_reading_duration_in_seconds' => ['filled', 'integer', 'min:1', 'gt:min_reading_duration_in_seconds'],
        ];
    }
}
