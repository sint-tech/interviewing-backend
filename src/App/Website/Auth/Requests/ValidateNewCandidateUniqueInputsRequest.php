<?php

namespace App\Website\Auth\Requests;

use Domain\Candidate\Models\Candidate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone;

class ValidateNewCandidateUniqueInputsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "email"             => ['required_without:mobile','prohibits:mobile',"string","email",Rule::unique(Candidate::class,"email")],
            "mobile"            => ['required_without:email',"prohibits:email","array:country,number"],
            "mobile.country"    => ["required_with:mobile",Rule::in(['EG'])],
            "mobile.number"     => [
                "required_with:mobile","integer", (new Phone())->country(['EG']),
                Rule::unique(Candidate::class,"mobile_number")->where("mobile_country",$this->input("mobile.country"))],
        ];
    }
}
