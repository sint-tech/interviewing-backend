<?php

namespace App\Organization\InterviewManagement\Queries;

use Domain\InterviewManagement\Models\Interview;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\QueryBuilderRequest;

class GetInterviewReportQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = Interview::query();

        parent::__construct($subject, $request);

        $this->allowedFilters(
            AllowedFilter::exact('interview_template_id'),
            AllowedFilter::exact('vacancy_id'),
            AllowedFilter::exact('status')->ignore(['accepted', 'passed']),
        );

        $this->disallowFilterByMultipleValues();

        $this->handleStatusFilter();
    }

    protected function handleStatusFilter(): self
    {
        if ($this->request->input('filter.status') === 'accepted') {
            $this->abortFilterVacancyIdRequired();
            $this->subject->whereAccepted(Vacancy::query()->findOrFail($this->request->input('filter.vacancy_id'))->open_positions);
        }

        if ($this->request->input('filter.status') == 'passed') {
            $this->abortFilterVacancyIdRequired();
            $this->subject->orderByAvgScoreDesc()->whereNotIn('id', Interview::query()->whereAccepted(Vacancy::query()->findOrFail($this->request->input('filter.vacancy_id'))->open_positions)->pluck('id'));
        }

        return $this;
    }

    private function disallowFilterByMultipleValues(): void
    {
        foreach ($this->allowedFilters as $allowedFilter) {
            $key = "filter.{$allowedFilter->getName()}";

            if ($this->request->missing($key)) {
                continue;
            }

            $this->checkMultipleValuesPassedForFilter($key);
        }
    }

    private function checkMultipleValuesPassedForFilter(string $filter_name): void
    {
        $count = count(explode(QueryBuilderRequest::getFilterArrayValueDelimiter(), $this->request->input($filter_name)));

        if ($count <= 1) {
            return;
        }

        $filter_name = str_replace('filter.', '', $filter_name);

        abort(400, "only supported filter by single value for key: $filter_name");
    }

    protected function abortFilterVacancyIdRequired(): void
    {
        $this->request->whenMissing('filter.vacancy_id',
            fn () => abort(400, 'filter by vacancy_id Must be filled when filter by status = \'accepted\'')
        );
    }
}
