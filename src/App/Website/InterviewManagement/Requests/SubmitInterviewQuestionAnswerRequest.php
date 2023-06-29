<?php

namespace App\Website\InterviewManagement\Requests;

use App\Website\InterviewManagement\Rules\AnswerVariantBelongsToQuestionVariantRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitInterviewQuestionAnswerRequest extends FormRequest
{
    public function authorize()
    {
        return $this->interview->candidate_id == auth()->id();
    }

    public function rules(): array
    {
        return [
            'question_occurrence_reason'    => ['required','string', /* todo set enum*/],
            'question_variant_id'   => ['required',
                Rule::exists('interview_template_questions','question_variant_id')
                    ->where('interview_template_id',$this->interview->interview_template_id)
                    ->withoutTrashed(),
            ],
            'answer_variant_id'     => ['required',Rule::exists('answer_variants','id'),
                new AnswerVariantBelongsToQuestionVariantRule($this->input('question_variant_id'))],
            'answer_text'           => ['required','string'],
            'score'                 => ['required','numeric','between:1,10' /*todo set min and max in interview answer config*/],
            'ml_video_semantics'       => ['required','json'],
            'ml_audio_semantics'       => ['required','json'],
            'ml_text_semantics'        => ['required','json'],
        ];
    }
}
