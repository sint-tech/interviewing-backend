<?php

namespace App\Candidate\Invitation\Queries;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Domain\Invitation\Models\Invitation;

class MyInvitationsQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = Invitation::query();

        parent::__construct($subject, $request);

        $this->allowedSorts([
            AllowedSort::custom('is_expired', new SortsInvitationByExpiration()),
            AllowedSort::field('last_invited_at'),
        ]);


        $this->allowedFilters([
            AllowedFilter::exact('is_expired')->ignore([null, 'false', 'true']),
        ]);


        $this->handleStatusFilter();
    }

    protected function handleStatusFilter(): self
    {
        if ($this->request->input('filter.is_expired') == 'true') {
            $this->subject->whereIsExpired();
        }

        if ($this->request->input('filter.is_expired') == 'false') {
            $this->subject->whereIsNotExpired();
        }

        return $this;
    }
}
