<?php

namespace App\Admin\QuestionManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionClusterUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'          => ['filled', 'string', 'min:3', 'max:255'],
            'description'   => ['nullable', 'string', 'min:2', 'max:10000'],
            'skills'        => ['filled', 'array', 'min:1'],
            'skills.*'      => [Rule::exists('skills', 'id')->withoutTrashed()],
        ];
    }
}
