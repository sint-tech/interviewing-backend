<?php

namespace App\Admin\QuestionManagement\Requests;

use Domain\QuestionManagement\Enums\QuestionTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class QuestionStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['nullable', 'string', 'min:1', 'max:1000'],
            'question_cluster_id' => ['required', Rule::exists('question_clusters', 'id')->withoutTrashed()],
            'difficult_level' => ['required', 'integer', 'min:1', 'max:10'],
            'question_type' => ['required', (new Enum(QuestionTypeEnum::class))],
            'min_reading_duration_in_seconds' => ['required', 'integer', 'min:1', 'lt:max_reading_duration_in_seconds'],
            'max_reading_duration_in_seconds' => ['required', 'integer', 'min:1', 'gt:min_reading_duration_in_seconds'],
            'default_ai_model_id' => ['required', Rule::exists('ai_models', 'id')->where('status', 'active')],
            'content_prompt' => ['required','string', function($attribute, $value, \Closure $fail) {
                if ($this->promptPlaceHolderDoesntExistInString($value,$placeholders = ['_INTERVIEWEE_ANSWER_','_QUESTION_TEXT_'])) {
                    $fail("prompt should contain all the placeholders: ".Arr::join($placeholders,', ',' and '));
                }
            }],
            'system_prompt' => ['required','string',function($attribute,$value,\Closure $fail) {
                if($this->promptPlaceHolderDoesntExistInString($value,$placeholder = '_RESPONSE_JSON_STRUCTURE_')) {
                    $fail("Prompt should contain placeholder: $placeholder");
                }
            }]
        ];
    }


    protected function promptPlaceHolderDoesntExistInString(string $haystack,string| array $placeHolder): bool
    {
        return ! Str::containsAll($haystack,Arr::wrap($placeHolder));
    }
}
