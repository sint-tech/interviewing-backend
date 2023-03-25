<?php

namespace App\Website\RegistrationReasons\Queries;

use Domain\Candidate\Models\RegistrationReasons;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class IndexRegistrationReasonQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = RegistrationReasons::query();

        parent::__construct($subject, $request);

        $this->allowedFilters(
            $this->getAllowedFilters()
        );
    }

    protected function getAllowedFilters():array
    {
        return [
            AllowedFilter::exact("id"),
            AllowedFilter::partial("title"),
            AllowedFilter::exact("availability_status"),
        ];
    }
}
