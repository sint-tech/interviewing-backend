<?php

namespace App\Admin\JobOpportunity\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Support\Rules\MorphRelationExistRule;

class JobOpportunityStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required','string','min:3','max:250'],
            'description' => ['nullable','string','min:1','max:900'],
            'started_at'  => ['nullable','date_format:Y-m-d H:m','after:now'],
            'ended_at'    => ['nullable','date_format:Y-m-d H:m','after:started_at'],
            'max_reconnection_tries' => ['required', 'min:0', 'max:5'],
            'open_positions' => ['required','integer','min:1']
        ];
    }
}
