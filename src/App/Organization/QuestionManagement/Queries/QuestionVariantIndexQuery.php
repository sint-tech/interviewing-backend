<?php
namespace App\Organization\QuestionManagement\Queries;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Domain\QuestionManagement\Models\QuestionVariant;

class QuestionVariantIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = QuestionVariant::query();

        parent::__construct($subject, $request);

        $this->setAllowedFilters();
        $this->allowedSorts('id', 'created_at', 'updated_at');

        $this->defaultSort('-updated_at');

        $this->allowedIncludes(
            AllowedInclude::relationship('organization')
        );
    }

    protected function setAllowedFilters(): self
    {
        return $this->allowedFilters(
            AllowedFilter::callback('status', function ($query, $value) {
                if ($value === 'private') {
                    return $query->where('organization_id', auth()->user()->organization_id);
                }
                return $query;
            }),
        );
    }
}
