<?php

namespace Domain\QuestionManagement\Actions;

use Domain\QuestionManagement\DataTransferObjects\QuestionClusterDto;
use Domain\QuestionManagement\Models\QuestionCluster;

class CreateQuestionClusterAction
{
    public function __construct(
        public QuestionClusterDto $questionClusterDto
    ) {

    }

    public function execute(): QuestionCluster
    {
        $question_cluster_data = array_merge(
            [
                'creator_id' => $this->questionClusterDto->creator->getKey(),
                'creator_type' => $this->questionClusterDto->creator->getMorphClass(),
            ],
            $this->questionClusterDto->toArray()
        );

        $question_cluster = (new QuestionCluster($question_cluster_data));

        $question_cluster->save();

        if (! empty($this->questionClusterDto->skills)) {
            $question_cluster->skills()->syncWithPivotValues($this->questionClusterDto->skills, [
                'assigner_id' => $this->questionClusterDto->creator->getKey(),
                'assigner_type' => $this->questionClusterDto->creator->getMorphClass(),
            ]);
        }

        return $question_cluster->load('skills');
    }
}
