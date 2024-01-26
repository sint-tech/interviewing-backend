<?php

namespace Domain\InterviewManagement\Actions;

use App\Exceptions\InterviewReachedMaxConnectionTriesException;
use Domain\InterviewManagement\Models\Interview;
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
            ->load(['questionVariants' => fn (Builder $builder) => $builder
                ->whereIntegerNotInRaw('question_variants.id', $interview->answers()->pluck('question_variant_id')),
            ]);

        return $interview;
    }
}
