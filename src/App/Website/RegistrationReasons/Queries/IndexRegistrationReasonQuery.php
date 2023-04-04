<?php

namespace App\Website\RegistrationReasons\Queries;

use Domain\Candidate\Models\RegistrationReason;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class IndexRegistrationReasonQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = RegistrationReason::query();

        parent::__construct($subject, $request);

        $this->allowedFilters(
            $this->getAllowedFilters()
        );
    }

    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::exact('id'),
            AllowedFilter::partial('title'),
            AllowedFilter::exact('availability_status'),
        ];
    }
}
