<?php

namespace App\Admin\QuestionManagement\Requests;

use Domain\QuestionManagement\Enums\QuestionClusterRecommendationEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class QuestionClusterRecommendationStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'question_cluster_id'   => ['required',Rule::exists('question_clusters','id')->withoutTrashed()],
            'type' => ['required',Rule::enum(QuestionClusterRecommendationEnum::class)],
            'min_score' => ['required','integer','between:1,10','lt:max_score'],
            'max_score' => ['required','integer','between:1,10','gt:min_score'],
            'statement' => ['required','string','min:3','max:1000'],
        ];
    }
}
