<?php

namespace App\Admin\JobTitle\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobTitleStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'availability_status' => ['required', 'string', 'in:active,inactive'],
        ];
    }
}
