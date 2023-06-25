<?php

namespace App\Admin\Skill\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SkillStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:1000'],
            'description' => ['nullable', 'string', 'min:3'],
        ];
    }
}
