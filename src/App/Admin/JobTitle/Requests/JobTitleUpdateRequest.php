<?php

namespace App\Admin\JobTitle\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobTitleUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['filled', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'availability_status' => ['filled', 'string', 'in:active,inactive'],
        ];
    }
}
