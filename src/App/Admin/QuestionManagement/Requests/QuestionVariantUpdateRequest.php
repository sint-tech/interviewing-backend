<?php

namespace App\Admin\QuestionManagement\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\QuestionManagement\Enums\QuestionVariantStatusEnum;

class QuestionVariantUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'text' => ['filled', 'string', 'min:3', 'max:1000'],
            'description' => ['string', 'min:3', 'max:1000'],
            'question_id' => ['filled', Rule::exists('questions', 'id')->withoutTrashed()],
            'status' => ['filled', new Enum(QuestionVariantStatusEnum::class)],
            'reading_time_in_seconds' => ['filled', 'integer', 'min:1'],
            'answering_time_in_seconds' => ['filled', 'integer', 'min:1'],
        ];
    }

    public function questionVariant(): QuestionVariant
    {
        return QuestionVariant::query()->findOrFail($this->question_variant);
    }
}
