<?php

namespace App\Website\Auth\Requests;

use Domain\Candidate\Models\Candidate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->guest();
    }

    public function rules(): array
    {
        return [
            "first_name"    => ["required","string","min:2","max:255"],
            "last_name"     => ["required","string","min:2","max:255"],
            "email"         => ["required","email",Rule::unique(Candidate::class,"email")],
            "password"      => ["required",Password::min(8)->letters()->mixedCase()->numbers()->symbols()]
        ];
    }
}
