<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\DataTransferObjects\InterviewDto;
use Domain\InterviewManagement\Models\Interview;
use Domain\QuestionManagement\Models\QuestionCluster;
use Domain\QuestionManagement\Models\QuestionVariant;
use Exception;
use Support\Scopes\ForAuthScope;

class CreateInterviewAction
{
    /**
     * @throws Exception
     */
    public function execute(InterviewDto $interviewDto): Interview
    {
        $this->noInterviewsRunningForCandidate($interviewDto->candidate_id);

        $interview = (new Interview())->fill($interviewDto->toArray());

        $interview->save();

        $interview->refresh()->load('questionClusters', 'questionVariants');

        $interview
            ->questionClusters
            ->each(fn (QuestionCluster $cluster) => $cluster
                ->setRelation('questionVariants', $interview->questionVariants
                    ->filter(fn (QuestionVariant $questionVariant) => $questionVariant->pivot->question_cluster_id == $cluster->getKey())
                )
            );

        return $interview;
    }

    /**
     * @throws Exception
     */
    protected function noInterviewsRunningForCandidate(int $candidateId): void
    {

        Interview::query()
            ->withoutGlobalScope(ForAuthScope::class)
            ->whereStatusNotInFinalStage()
            ->whereCandidate($candidateId)
            ->doesntExistOr(
                fn () => throw new Exception(
                    sprintf('can\'t create or start an interview until the running interview for candidate with id: %s is finished', $candidateId)
                )
            );
    }
}
