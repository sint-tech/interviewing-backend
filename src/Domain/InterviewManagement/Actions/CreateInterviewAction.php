<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\DataTransferObjects\InterviewDto;
use Domain\InterviewManagement\Models\Interview;
use Domain\QuestionManagement\Models\QuestionCluster;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Database\Eloquent\Builder;

class CreateInterviewAction
{
    public function __construct(
        public readonly InterviewDto $interviewDto
    )
    {
    }

    public function execute():Interview
    {
        $interview = (new Interview())->fill($this->interviewDto->toArray());

        $interview->save();

        $interview->refresh()->load('questionClusters','questionVariants');

        $interview->questionClusters->each(fn(QuestionCluster $cluster) => $cluster
            ->setRelation('questionVariants',$interview->questionVariants
                ->filter(fn(QuestionVariant $questionVariant) => $questionVariant->pivot->question_cluster_id == $cluster->getKey())
            )
        );

        return $interview;
    }
}
