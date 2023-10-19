<?php

namespace App\Organization\EmployeeManagement\Requests;

use Domain\Organization\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:3', 'max:40'],
            'last_name' => ['required', 'string', 'min:3', 'max:40'],
            'email' => ['required', 'email', Rule::unique(table_name(Employee::class), 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
