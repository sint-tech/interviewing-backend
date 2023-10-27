<?php

namespace App\Admin\QuestionManagement\Queries;

use Domain\QuestionManagement\Models\QuestionCluster;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
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

        $this->allowedSorts(
            'id',
            'created_at'
        );

        $this->allowedIncludes(
            $this->getAllowedIncludes()
        );

        $this->defaultSort('-created_at');
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::exact('id'),
            AllowedFilter::partial('name'),
            AllowedFilter::exact('skills.id'),
        ];
    }

    protected function getAllowedIncludes(): array
    {
        return [
            AllowedInclude::relationship('skills'),
            AllowedInclude::relationship('variants', 'questionVariants'),
            AllowedInclude::relationship('questions'),
            AllowedInclude::relationship('questions.questionVariants'),
        ];
    }
}
