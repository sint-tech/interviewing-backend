<?php

namespace App\Admin\Organization\Requests;

use Domain\Organization\Models\Employee;
use Domain\Organization\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique(Employee::class, 'id')],
            'is_organization_manager' => ['required', 'boolean'],
            'organization_id' => ['required', Rule::exists(Organization::class, 'id')],
            'password' => ['required', 'confirmed', 'min:8'],
        ];
    }
}
