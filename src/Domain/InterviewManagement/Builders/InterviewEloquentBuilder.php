<?php

namespace Domain\InterviewManagement\Builders;

use Domain\Candidate\Models\Candidate;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\Models\Answer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * @mixin Builder
 */
class InterviewEloquentBuilder extends Builder
{
    public function whereStatus(InterviewStatusEnum $status, string $operator = '=', string $boolean = 'and'): self
    {
        return $this->where('status', '=', $status->value, $boolean);
    }

    public function whereStatusIn(array $statuses, string $boolean = 'and'): self
    {
        $values = [];

        foreach ($statuses as $status) {
            throw_unless($status instanceof InterviewStatusEnum);

            $values[] = $status->value;
        }

        return $this->whereIn('status', $values);
    }

    public function whereStatusInFinalStage(string $boolean = 'and', bool $not = false): self
    {
        return $this->whereIn('status', InterviewStatusEnum::endedStatuses(), $boolean, $not);
    }

    public function whereStatusNotInFinalStage(): self
    {
        return $this->whereStatusInFinalStage(not: true);
    }

    public function whereIsEnded(bool $ended = true, string $boolean = 'and'): self
    {
        return $this->whereNull('ended_at', $boolean, $ended);
    }

    public function whereCandidate(Candidate|int $candidate): self
    {
        return is_int($candidate) ?
            $this->where('candidate_id', $candidate) :
            $this->whereBelongsTo($candidate, 'candidate');
    }

    public function orderByAvgScore(string $direction = 'ASC'): self
    {
        $answers_table = (new Answer())->getTable();

        $this->orderBy(Answer::query()
            ->select(DB::raw('AVG(score)'))
            ->whereColumn(
                "$answers_table.interview_id",
                '=',
                "{$this->getModel()->getTable()}.id"
            )
            ->groupBy('interview_id'),
            $direction
        );

        return $this;
    }

    public function orderByAvgScoreDesc(): self
    {
        return $this->orderByAvgScore('DESC');
    }

    /**
     * get the top candidates passed the interview
     */
    public function whereAccepted(int $open_positions = 1): self
    {
        return $this->wherePassed()->take($open_positions);
    }

    public function wherePassed(): self
    {
        return $this->orderByAvgScoreDesc()
            ->whereStatus(InterviewStatusEnum::Passed);
    }
}
