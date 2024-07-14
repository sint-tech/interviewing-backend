<?php

namespace Domain\InterviewManagement\Builders;

use Domain\Candidate\Models\Candidate;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\Models\Answer;
use Domain\InterviewManagement\Models\Interview;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public function whereRunning(): self
    {
        return $this->where(function (self $builder) {
            return $builder->whereStatusNotInFinalStage()
                ->whereNotEnded('OR');
        });
    }

    public function whereIsEnded(bool $ended = true, string $boolean = 'and'): self
    {
        return $this->whereNull('ended_at', $boolean, $ended);
    }

    public function whereNotEnded(string $boolean = 'and'): self
    {
        return $this->whereIsEnded(false, $boolean);
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

        $this->orderBy(
            Answer::query()
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
    public function whereAccepted(int $open_positions = 1, int $vacancy_id): self
    {
        $selected = Interview::where('vacancy_id', $vacancy_id)->whereSelected()->select('id')->get();
        $remaining_positions = $open_positions - $selected->count();

        if ($remaining_positions <= 0) {
            return $this->whereIn('id', $selected->pluck('id'));
        }

        $passed = Interview::where('vacancy_id', $vacancy_id)->wherePassed()->take($remaining_positions)->select('id')->get();

        $accepted = $passed->merge($selected);

        return $this->whereIn('id', $accepted->pluck('id'));
    }

    public function wherePassed(): self
    {
        return $this->orderByAvgScoreDesc()
            ->whereStatus(InterviewStatusEnum::Passed);
    }

    public function whereSelected(): self
    {
        return $this->whereStatus(InterviewStatusEnum::Selected);
    }

    public function whereRejected(string $boolean = 'and'): self
    {
        return $this->whereStatus(InterviewStatusEnum::Rejected, boolean: $boolean);
    }

    public function whereReachedMaxTries(bool $reached_max_tries = true, string $boolean = 'and'): self
    {
        $vacancy_builder = function (Builder $builder) use ($reached_max_tries) {
            return $builder
                ->whereColumn(
                    'interviews.connection_tries',
                    $reached_max_tries ? '>=' : '<',
                    'vacancies.max_reconnection_tries'
                )->limit(1);
        };

        return $boolean == 'and' ? $this->whereHas('vacancy', $vacancy_builder) : $this->orWhereHas('vacancy', $vacancy_builder);
    }
}
