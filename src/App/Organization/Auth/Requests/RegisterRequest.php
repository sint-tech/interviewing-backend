<?php

namespace App\Organization\Auth\Requests;

use Domain\Organization\Enums\OrganizationEmployeesRangeEnum;
use Domain\Organization\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guest();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:1000'],
            'website_url' => ['nullable', 'url'],
            'address' => ['nullable', 'string', 'max:500'],
            'contact_email' => ['nullable', 'email'],
            'industry' => ['nullable', 'string', 'max:255'],
            'number_of_employees' => ['nullable', Rule::enum(OrganizationEmployeesRangeEnum::class)],
            'manager' => ['required', 'array'],
            'manager.first_name' => ['required', 'string', 'min:3', 'max:40'],
            'manager.last_name' => ['required', 'string', 'min:3', 'max:40'],
            'manager.email' => ['required', Rule::unique(table_name(Employee::class), 'email')],
            'manager.password' => ['required', 'string', Password::min(8)->letters()->numbers(), 'confirmed'],
            'logo' => ['filled', 'image'],
        ];
    }
}
