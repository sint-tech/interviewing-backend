<?php

namespace App\Admin\Skill\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SkillUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['filled', 'string', 'min:3', 'max:1000'],
            'description' => ['nullable', 'string', 'min:3'],
        ];
    }
}
