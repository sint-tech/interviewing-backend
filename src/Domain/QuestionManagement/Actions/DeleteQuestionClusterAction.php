<?php

namespace Domain\QuestionManagement\Actions;

use Domain\QuestionManagement\Models\Question;
use Domain\QuestionManagement\Models\QuestionCluster;

class DeleteQuestionClusterAction
{
    public function __construct(
        public int $questionCluster
    ) {

    }

    public function execute(): QuestionCluster
    {
        $questionCluster = QuestionCluster::query()->findOrFail($this->questionCluster);

        $questionCluster->load('questions');

        $this->deleteClusterQuestions($questionCluster);

        $questionCluster->delete();

        return $questionCluster;
    }

    protected function deleteClusterQuestions(QuestionCluster $cluster)
    {
        $cluster->questions->each(fn (Question $question) => (new DeleteQuestionAction($question->getKey()))->execute());
    }
}
