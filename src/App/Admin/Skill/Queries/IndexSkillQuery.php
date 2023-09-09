<?php

namespace App\Admin\Skill\Queries;

use Domain\Skill\Models\Skill;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class IndexSkillQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = Skill::query();

        parent::__construct($subject, $request);

        $this->allowedFilters(
            $this->getAllowedFilters()
        );

        $this->defaultSorts('-updated_at');

        $this->allowedSorts(
            AllowedSort::field('created_at','id')
        );

        $this->allowedIncludes(
            AllowedInclude::relationship('questionClusters')
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
