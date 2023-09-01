<?php

namespace Domain\QuestionManagement\Actions;

use Domain\QuestionManagement\DataTransferObjects\QuestionClusterDto;
use Domain\QuestionManagement\Models\QuestionCluster;

class UpdateQuestionClusterAction
{
    public function __construct(
        public QuestionCluster $questionCluster,
        public readonly QuestionClusterDto $questionClusterDto,
    ) {
    }

    public function execute(): QuestionCluster
    {
        $this->questionCluster->append([
            'creator_id' => $this->questionClusterDto->creator->getKey(),
            'creator_type' => $this->questionClusterDto->creator::class,
        ]);

        $this->questionCluster->update($this->questionClusterDto->except('skills')->toArray());

        if (! empty($this->questionClusterDto->skills)) {
            $this->questionCluster->skills()->syncWithPivotValues($this->questionClusterDto->skills, [
                'assigner_id' => $this->questionClusterDto->creator->getKey(),
                'assigner_type' => $this->questionClusterDto->creator::class,
            ]);
        }

        return $this->questionCluster->refresh()->load([
            'skills',
        ]);
    }
}
