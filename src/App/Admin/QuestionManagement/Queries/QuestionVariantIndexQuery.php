<?php

namespace App\Admin\QuestionManagement\Queries;

use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class QuestionVariantIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = QuestionVariant::query();

        parent::__construct($subject, $request);

        $this->allowedFilters($this->getAllowedFilters());

        $this->allowedSorts('id','created_at','updated_at');

        $this->defaultSort('-updated_at');
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::exact('id'),
        ];
    }
}
