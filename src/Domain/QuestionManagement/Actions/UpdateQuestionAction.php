<?php

namespace Domain\QuestionManagement\Actions;

use Domain\QuestionManagement\DataTransferObjects\QuestionData;
use Domain\QuestionManagement\Models\Question;

class UpdateQuestionAction
{
    public function __construct(
        public Question $question,
        public readonly QuestionData $questionData
    ) {
    }

    public function execute(): Question
    {
        $this->question->update($this->questionData->except('ai_prompt')->toArray());

        $this->question->defaultAIPrompt->update($this->questionData->ai_prompt);

        return $this->question->refresh()->load([
            'questionCluster',
        ]);
    }
}
