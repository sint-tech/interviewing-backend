<?php

namespace App\Organization\Settings\Requests;

use Domain\Organization\Enums\OrganizationEmployeesRangeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrganizationUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['filled', 'string', 'min:3', 'max:1000'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'website_url' => ['nullable', 'url'],
            'number_of_employees' => ['nullable', Rule::enum(OrganizationEmployeesRangeEnum::class)],
            'industry' => ['nullable', 'string', 'min3', 'max:255'],
        ];
    }
}
