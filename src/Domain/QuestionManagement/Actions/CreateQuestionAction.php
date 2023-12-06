<?php

namespace Domain\QuestionManagement\Actions;

use Domain\AiPromptMessageManagement\Enums\AiModelEnum;
use Domain\QuestionManagement\DataTransferObjects\QuestionData;
use Domain\QuestionManagement\Models\Question;
use Spatie\LaravelData\Optional;

class CreateQuestionAction
{
    public function execute(QuestionData $questionData): Question
    {
        $data = $questionData->except('creator')->toArray();

        if ($questionData->default_ai_model_id instanceof Optional) {
            $data['default_ai_model'] = AiModelEnum::Gpt_3_5;
        }

        $question = new Question($data);

        $question->save();

        return $question->load(['creator', 'questionCluster', 'questionVariants']);
    }
}
