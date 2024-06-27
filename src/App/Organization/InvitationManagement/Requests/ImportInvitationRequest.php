<?php

namespace App\Organization\InvitationManagement\Requests;

use Domain\Vacancy\Models\Vacancy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Validator;

class ImportInvitationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required', File::types([
                'application/csv', 'application/excel',
                'application/vnd.ms-excel', 'application/vnd.msexcel',
                'text/csv', 'text/anytext', 'text/plain', 'text/x-c',
                'text/comma-separated-values',
                'inode/x-empty', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'csv', 'xlsx']
            )],
            'vacancy_id' => ['required', 'integer', Rule::exists(table_name(Vacancy::class), 'id')->withoutTrashed()],
            'should_be_invited_at' => ['required', 'date', 'date_format:Y-m-d H:i', 'after:now'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->interviewTemplateIsNotOpenForVacancy()) {
                    $validator->errors()->add('interview_template_id', __('interview template id not open to this vacancy'));
                }
            },
        ];
    }

    private function interviewTemplateIsNotOpenForVacancy(): bool
    {
        if ($this->isNotFilled('interview_template_id')) {
            return false;
        }

        return Vacancy::query()->whereKey($this->validated('vacancy_id'))->first()
            ->defaultInterviewTemplate
            ->getKey() !== $this->validated('interview_template_id');
    }
}
