<?php

namespace Domain\QuestionManagement\Actions;

use Domain\QuestionManagement\DataTransferObjects\QuestionData;
use Domain\QuestionManagement\Models\Question;

class UpdateQuestionAction
{
    public function __construct(
        public Question $question,
        public readonly QuestionData $questionData
    )
    {
    }

    public function execute():Question
    {
        $this->question->update($this->questionData->toArray());

        return $this->question->refresh()->load([
            'questionCluster'
        ]);
    }
}
