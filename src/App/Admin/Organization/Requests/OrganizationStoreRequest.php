<?php

namespace App\Admin\Organization\Requests;

use Domain\Organization\Enums\OrganizationEmployeesRangeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class OrganizationStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:1000'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'website_url' => ['nullable', 'url'],
            'number_of_employees' => ['nullable', Rule::enum(OrganizationEmployeesRangeEnum::class)],
            'industry' => ['nullable', 'string', 'min3', 'max:255'],
            'manager' => ['required', 'array'],
            'manager.first_name' => ['required', 'string', 'min:3', 'max:1000'],
            'manager.last_name' => ['required', 'string', 'min:3', 'max:1000'],
            'manager.email' => ['required', Rule::unique('organization_employees', 'email')],
            'manager.password' => ['required', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'logo' => ['filled', 'image'],
        ];
    }
}
