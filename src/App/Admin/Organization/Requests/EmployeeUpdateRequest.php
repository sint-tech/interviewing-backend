<?php

namespace App\Admin\Organization\Requests;

use Domain\Organization\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['filled', 'string', 'max:255'],
            'last_name' => ['filled', 'string', 'max:255'],
            'email' => ['filled', 'email', Rule::unique(Employee::class, 'id')->ignore($this->route('employee')->getKey())],
            'is_organization_manager' => ['filled', 'boolean'],
        ];
    }
}
