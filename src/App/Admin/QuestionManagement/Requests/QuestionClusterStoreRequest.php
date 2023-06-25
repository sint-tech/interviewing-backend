<?php

namespace App\Admin\QuestionManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionClusterStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'min:2', 'max:10000'],
            'skills' => ['required', 'array', 'min:1'],
            'skills.*' => [Rule::exists('skills', 'id')->withoutTrashed()],
        ];
    }
}
