<?php

namespace App\Admin\QuestionManagement\Requests;

use App\Admin\QuestionManagement\Requests\Traits\QuestionVariantOwnerTrait;
use Domain\QuestionManagement\Enums\QuestionVariantOwnerEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Support\Rules\MorphRelationExistRule;

class QuestionVariantUpdateRequest extends FormRequest
{
    use QuestionVariantOwnerTrait;

    public function rules(): array
    {
        return [
            'text' => ['filled', 'string', 'min:3', 'max:1000'],
            'description' => ['string', 'min:3', 'max:1000'],
            'question_id' => ['filled', Rule::exists('questions', 'id')->withoutTrashed()],
            'reading_time_in_seconds' => ['filled', 'integer', 'min:1'],
            'answering_time_in_seconds' => ['filled', 'integer', 'min:1'],
            'owner' => ['filled',
                'array',
                (new MorphRelationExistRule(
                    QuestionVariantOwnerEnum::class,
                    $this->input('owner.model_type') == 'admin' ? 'users' : null)
                ),
            ],
        ];
    }
}
