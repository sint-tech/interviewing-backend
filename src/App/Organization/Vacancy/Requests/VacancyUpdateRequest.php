<?php

namespace App\Organization\Vacancy\Requests;

use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class VacancyUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['filled', 'min:3', 'max:250'],
            'description' => ['nullable', 'min:3', 'max:1000'],
            'max_reconnection_tries' => ['filled', 'min:0', 'max:1000'],
            'open_positions' => ['filled', 'min:1', 'max:1000'],
            'started_at' => ['nullable', 'date_format:Y-m-d H:i', 'after:now'],
            'ended_at' => ['nullable', 'date_format:Y-m-d H:i', 'after:started_at'],
            'interview_template_id' => ['filled', 'int', Rule::exists(table_name(InterviewTemplate::class), 'id')->withoutTrashed()],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->vacancy()->interviews()->exists()) {
                    $validator->errors()->add('vacancy', "You can't update this vacancy, It has interviews running on this vacancy.");
                }
                if ($this->vacancy()->is_ended) {
                    $validator->errors()->add('vacancy', "This Vacancy can't be updated as it's already ended.");
                }
            },
        ];
    }

    public function vacancy(): Vacancy
    {
        if ($this->route('vacancy') instanceof Vacancy) {
            return $this->route('vacancy');
        }

        return Vacancy::query()->findOrFail($this->route('vacancy'));
    }
}
