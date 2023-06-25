<?php

namespace App\Admin\QuestionManagement\Queries;

use Domain\QuestionManagement\Models\QuestionCluster;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class QuestionClusterIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = QuestionCluster::query();

        parent::__construct($subject, $request);

        $this->allowedFilters(
            $this->getAllowedFilters()
        );
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::exact('id'),
            AllowedFilter::partial('name'),
        ];
    }
}
