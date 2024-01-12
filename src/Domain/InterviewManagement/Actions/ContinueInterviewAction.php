<?php

namespace Domain\InterviewManagement\Actions;

use App\Exceptions\InterviewReachedMaxConnectionTriesException;
use Domain\InterviewManagement\Models\Interview;
use Domain\QuestionManagement\Models\QuestionCluster;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ContinueInterviewAction
{
    public function execute(Interview $interview): Interview
    {
        if (app(InterviewReachedMaxConnectionTriesAction::class)->execute($interview)) {
            throw new InterviewReachedMaxConnectionTriesException();
        }

        Interview::query()->whereKey($interview)->increment('connection_tries');

        $interview->refresh()
            ->load(['questionClusters',
                'questionVariants' => fn (Builder $builder) => $builder
                    ->whereIntegerNotInRaw('question_variants.id', $interview->answers()->pluck('question_variant_id')),
            ]);

        $interview
            ->questionClusters
            ->each(fn (QuestionCluster $cluster) => $cluster
                ->setRelation('questionVariants', $interview->questionVariants
                    ->filter(fn (QuestionVariant $questionVariant) => $questionVariant->pivot->question_cluster_id == $cluster->getKey())
                )
            );

        return $interview;
    }
}
