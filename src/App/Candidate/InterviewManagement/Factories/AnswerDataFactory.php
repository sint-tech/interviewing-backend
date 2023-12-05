<?php

namespace App\Candidate\InterviewManagement\Factories;

use App\Candidate\InterviewManagement\Requests\SubmitInterviewQuestionAnswerRequest;
use Domain\AiPromptMessageManagement\Actions\PromptAnswerAnalyticsAction;
use Domain\AiPromptMessageManagement\Models\AIModel;
use Domain\AiPromptMessageManagement\Models\AiPromptMessage;
use Domain\InterviewManagement\DataTransferObjects\AnswerDto;

class AnswerDataFactory
{
    public static function fromRequest(SubmitInterviewQuestionAnswerRequest $request): AnswerDto
    {
        return AnswerDto::from(
            array_merge(
                $request->validated(),
                [
                    'interview_id' => $request->interview()->getKey(),
                ]
            )
        );
    }

    //    private function getMlTextSemantics(SubmitInterviewQuestionAnswerRequest $request): string
    //    {
    //        $promptAiModel = $request->questionVariant()->defaultAiPromptMessage()
    //            ->firstOr(fn () => AiPromptMessage::query()->create([
    //                'ai_model_id' => AIModel::query()->firstOrCreate()->getKey(),
    //                'prompt_text' => 'temp prompt message',
    //                'question_variant_id' => $request->validated('question_variant_id'),
    //            ])->refresh());
    //
    //        return (new PromptAnswerAnalyticsAction(
    //            $promptAiModel,
    //            $request->validated('answer_text')
    //        ))->execute();
    //    }
}
