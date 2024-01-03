<?php

namespace App\Organization\EmployeeManagement\Requests;

use Domain\Organization\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeStoreRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->is_organization_manager;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:3', 'max:40'],
            'last_name' => ['required', 'string', 'min:3', 'max:40'],
            'email' => ['required', 'email', Rule::unique(table_name(Employee::class), 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_organization_manager' => ['filled', 'boolean']
        ];
    }

    protected function passedValidation()
    {
        $this->mergeIfMissing(['is_organization_manager' => false]);
    }
}
