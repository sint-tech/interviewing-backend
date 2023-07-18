<?php

namespace App\Admin\QuestionManagement\Queries;

use Domain\QuestionManagement\Models\QuestionClusterRecommendation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class QuestionClusterRecommendationQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = QuestionClusterRecommendation::query();

        parent::__construct($subject, $request);

        $this->allowedFilters(
            AllowedFilter::exact('id'),
            AllowedFilter::partial('segment'),
            AllowedFilter::exact('type')
        );

        $this->allowedSorts(
            'created_at',
            'id'
        );

        $this->defaultSort('-id');
    }
}
