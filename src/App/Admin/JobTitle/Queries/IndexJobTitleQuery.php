<?php

namespace App\Admin\JobTitle\Queries;

use Domain\JobTitle\Models\JobTitle;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class IndexJobTitleQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = JobTitle::query();

        parent::__construct($subject, $request);
    }
}
