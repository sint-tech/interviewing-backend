<?php

namespace App\Admin\Organization\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CreateOrganizationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'      => ['required','string','min:3','max:1000'],
            'manager'   => ['required','array'],
            'manager.first_name'    => ['required','string','min:3','max:1000'],
            'manager.last_name'     => ['required','string','min:3','max:1000'],
            'manager.email'         => ['required',Rule::unique('employees','email')],
            'manager.password'      => ['required','string',Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ];
    }
}
