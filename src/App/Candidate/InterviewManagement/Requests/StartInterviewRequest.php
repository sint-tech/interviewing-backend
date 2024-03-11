<?php

namespace App\Candidate\InterviewManagement\Requests;

use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StartInterviewRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'vacancy_id' => ['required', 'int', Rule::exists(table_name(Vacancy::class), 'id')->withoutTrashed()],
            'interview_template_id' => [
                'nullable',
                'int',
                Rule::exists(InterviewTemplate::class, 'id')
                    ->withoutTrashed(),
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->interviewTemplateIsNotOpenForVacancy()) {
                    $validator->errors()->add('interview_template_id', __('interview template id not open to this vacancy'));
                }
                if ($this->vacancy()->is_ended) {
                    $validator->errors()->add('vacancy_id', __('vacancy is ended'));
                }
                if (! $this->vacancy()->is_started) {
                    $validator->errors()->add('vacancy_id', __('vacancy is not started'));
                }
            },
        ];
    }

    public function vacancy(): Vacancy
    {
        return Vacancy::query()->find($this->validated('vacancy_id'));
    }

    public function interviewTemplate(): InterviewTemplate
    {
        $vacancy = $this->vacancy();

        if ($this->isNotFilled('interview_template_id')) {
            return $vacancy->defaultInterviewTemplate;
        }

        return InterviewTemplate::query()->whereKey($this->validated('interview_template_id'))->first();
    }

    private function interviewTemplateIsNotOpenForVacancy(): bool
    {
        if ($this->isNotFilled('interview_template_id')) {
            return false;
        }

        return $this->vacancy()->defaultInterviewTemplate()->isNot($this->interviewTemplate());
    }
}
