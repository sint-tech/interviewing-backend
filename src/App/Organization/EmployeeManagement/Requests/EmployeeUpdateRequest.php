<?php

namespace App\Organization\EmployeeManagement\Requests;

use Domain\Organization\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->is_organization_manager;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['filled', 'string', 'min:3', 'max:40'],
            'last_name' => ['filled', 'string', 'min:3', 'max:40'],
            'email' => ['filled', 'email', Rule::unique(table_name(Employee::class), 'email')->ignore($this->route('employee')->getKey())],
            'is_organization_manager' => ['filled', 'boolean'],
        ];
    }
}
