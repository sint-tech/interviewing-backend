<?php

namespace App\Admin\InterviewManagement\Queries;

use Domain\InterviewManagement\Models\Interview;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class InterviewIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = Interview::query();

        parent::__construct($subject, $request);

        $this->allowedFilters([
            AllowedFilter::exact('status')->ignore('accepted'),
        ]);

        $this->handleStatusFilter();
    }

    protected function handleStatusFilter(): self
    {
        if ($this->request->input('filter.status') === 'accepted') {
            $this->subject->whereAccepted();
        }

        if ($this->request->input('filter.status') == 'passed') {
            $this->subject->orderByAvgScoreDesc()->whereNotIn('id', Interview::query()->whereAccepted()->pluck('id'));
        }

        return $this;
    }
}
