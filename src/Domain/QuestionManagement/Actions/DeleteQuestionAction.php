<?php

namespace Domain\QuestionManagement\Actions;

use Domain\QuestionManagement\Models\Question;

class DeleteQuestionAction
{
    public function __construct
    (
        public readonly int $question_id
    )
    {
    }

    public function execute(): Question
    {
        $question = Question::query()->findOrFail($this->question_id);

        $question->delete();

        return $question;
    }
}
