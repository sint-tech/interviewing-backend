<?php

namespace Domain\QuestionManagement\Actions;

use Domain\AiPromptMessageManagement\Enums\PromptMessageStatus;
use Domain\AiPromptMessageManagement\Models\AIPrompt;
use Domain\QuestionManagement\DataTransferObjects\QuestionData;
use Domain\QuestionManagement\Models\Question;

class CreateQuestionAction
{
    public function execute(QuestionData $questionData): Question
    {
        $data = $questionData->except('creator')->toArray();

        $question = new Question($data);

        $question->save();

        $this->createDefaultAIPrompt($question, $questionData->ai_prompt);

        return $question->load(['creator', 'questionCluster', 'questionVariants']);
    }

    protected function createDefaultAIPrompt(Question $question, array $ai_prompt_data): AIPrompt
    {
        $ai_prompt_data = array_merge($ai_prompt_data, [
            'weight' => 100,
            'status' => PromptMessageStatus::Enabled,
        ]);

        return $question->defaultAIPrompt()->create($ai_prompt_data);
    }
}
