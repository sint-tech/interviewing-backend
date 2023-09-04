<?php

namespace App\Admin\QuestionManagement\Requests;

use App\Admin\QuestionManagement\Requests\Traits\QuestionVariantOwnerTrait;
use Domain\QuestionManagement\Enums\QuestionVariantOwnerEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Support\Rules\MorphRelationExistRule;

class QuestionVariantStoreRequest extends FormRequest
{
    use QuestionVariantOwnerTrait;

    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'min:3', 'max:1000'],
            'description' => ['string', 'min:3', 'max:1000'],
            'question_id' => ['required', Rule::exists('questions', 'id')->withoutTrashed()],
            'reading_time_in_seconds' => ['required', 'integer', 'min:1'],
            'answering_time_in_seconds' => ['required', 'integer', 'min:1'],
            'owner' => ['required',
                'array',
                (new MorphRelationExistRule(
                    QuestionVariantOwnerEnum::class,
                    $this->input('owner.model_type') == 'admin' ? 'users' : null)
                ),
            ],
            'ai_model_ids' => ['filled','array','min:1'],
            'ai_model_ids.*'   => ['integer',Rule::exists('ai_models','id')->where('status','active')]
        ];
    }
}
