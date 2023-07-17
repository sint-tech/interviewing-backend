<?php

namespace App\Candidate\InterviewManagement\Factories;

use App\Candidate\InterviewManagement\Requests\SubmitInterviewQuestionAnswerRequest;
use Domain\AiPromptMessageManagement\Actions\PromptAnswerAnalyticsAction;
use Domain\AiPromptMessageManagement\Models\AiPromptMessage;
use Domain\InterviewManagement\DataTransferObjects\AnswerDto;

class AnswerDataFactory
{
    public static function fromRequest(SubmitInterviewQuestionAnswerRequest $request):AnswerDto
    {
        $ml_text_semantics = (new self())->getMlTextSemantics($request);

        return AnswerDto::from(
            array_merge(
                $request->validated(),
                [
                    'interview_id' => $request->route()->parameter('interview.id'),
                    'ml_text_semantics' => $ml_text_semantics,
                    'score' => data_get(json_decode($ml_text_semantics,true),'rate',1),
                ]
            )
        );
    }


    /**
     * @param SubmitInterviewQuestionAnswerRequest $request
     * @return string
     */
    private function getMlTextSemantics(SubmitInterviewQuestionAnswerRequest $request):string
    {
        return (new PromptAnswerAnalyticsAction(
            $request->questionVariant()->defaultAiPromptMessage()
                ->firstOr(fn() => AiPromptMessage::query()->create([
                    'prompt_text' => 'temp prompt message',
                    'question_variant_id'   => $request->validated('question_variant_id')
                ])),
            $request->validated('answer_text')
        ))->execute();
    }
}
