<?php

namespace App\Admin\InvitationManagement\Queries;

use Domain\Invitation\Models\Invitation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class InvitationIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = Invitation::query();

        parent::__construct($subject, $request);

        $this->allowedFilters(
            AllowedFilter::exact('interview_template_id')
        );
    }
}
