<?php

namespace Domain\QuestionManagement\Actions;

use Domain\QuestionManagement\DataTransferObjects\QuestionData;
use Domain\QuestionManagement\Models\Question;

class CreateQuestionAction
{
    public function __construct(
        public readonly QuestionData $questionData
    ) {

    }

    public function execute(): Question
    {
        $data = array_merge(
            [
                'creator_id' => $this->questionData->creator->getKey(),
                'creator_type' => $this->questionData->creator::class,
            ], $this->questionData->toArray()
        );

        $question = (new Question($data));

        $question->save();

        return $question->load(['creator', 'questionCluster']);
    }
}
