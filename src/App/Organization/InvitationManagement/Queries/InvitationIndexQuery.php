<?php

namespace App\Organization\InvitationManagement\Queries;

use Domain\Invitation\Models\Invitation;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

class InvitationIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = Invitation::query();

        parent::__construct($subject, $request);

        $this->allowedFilters(
            AllowedFilter::exact('interview_template_id'),
            AllowedFilter::exact('invitation_id'),
            AllowedFilter::exact('id'),
        );

        $this->allowedIncludes(
            AllowedInclude::relationship('vacancy'),
            AllowedInclude::relationship('interviewTemplate'),
        );

        $this->defaultSort('-id');
    }
}
