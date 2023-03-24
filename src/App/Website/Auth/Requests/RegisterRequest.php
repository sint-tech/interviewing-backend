<?php

namespace App\Website\Auth\Requests;

use Domain\Candidate\Models\Candidate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Propaganistas\LaravelPhone\Rules\Phone;

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
            "mobile"        => ["required","array:country,number"],
            "mobile.country" => ["required",Rule::in(['EG'])],
            "mobile.number"  => [
                "required","integer", (new Phone())->country(['EG']),
                Rule::unique(Candidate::class,"mobile_number")->where("mobile_country",$this->input("mobile.country"))],
            "password"      => ["required",Password::min(8)->letters()->mixedCase()->numbers()->symbols()]
        ];
    }
}
