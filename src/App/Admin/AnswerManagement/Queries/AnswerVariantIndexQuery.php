<?php

namespace App\Admin\AnswerManagement\Queries;

use Domain\AnswerManagement\Models\AnswerVariant;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AnswerVariantIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = AnswerVariant::query();

        parent::__construct($subject, $request);

        $this->setAllowedFilters();

        $this->defaultSort('-id');
    }

    /**
     * @return AnswerVariantIndexQuery
     */
    protected function setAllowedFilters(): self
    {
        return $this->allowedFilters(
            AllowedFilter::exact('id'),
            AllowedFilter::exact('answer_id','answer_id'),
            AllowedFilter::exact('interviews','interviews.id'),
        );
    }
}
