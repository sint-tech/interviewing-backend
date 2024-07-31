<?php

namespace App\Candidate\Invitation\Requests;

use Illuminate\Validation\Rule;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Validation\Validator;
use Domain\Invitation\Models\Invitation;
use Illuminate\Foundation\Http\FormRequest;

class ExternalInviteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'vacancy_id' => [
                'required', 'int', Rule::exists(table_name(Vacancy::class), 'id')->withoutTrashed(),
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (Invitation::query()->where('email', $this->input('email'))->where('vacancy_id', $value)->exists()) {
                        $fail(__('This invitation had create/sent before'));
                    }
                },
            ],
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->vacancy()->is_ended) {
                    $validator->errors()->add('vacancy_id', __('vacancy is ended'));
                }
            },
        ];
    }

    public function vacancy(): Vacancy
    {
        return Vacancy::query()->find($this->validated('vacancy_id'));
    }
}
