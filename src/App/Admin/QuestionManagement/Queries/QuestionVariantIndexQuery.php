<?php

namespace App\Admin\QuestionManagement\Queries;

use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Database\Eloquent\Model;
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
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::exact('id'),
        ];
    }
}
