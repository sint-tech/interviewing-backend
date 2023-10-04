<?php

namespace App\Admin\InvitationManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class ImportInvitationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file'  => ['required',File::types([
                    'application/csv','application/excel',
                    'application/vnd.ms-excel', 'application/vnd.msexcel',
                    'text/csv', 'text/anytext', 'text/plain', 'text/x-c',
                    'text/comma-separated-values',
                    'inode/x-empty', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            )],
            'interview_template_id' => ['required', 'integer', Rule::exists('interview_templates','id')->withoutTrashed()],
            'should_be_invited_at'  => ['required','date','date_format:Y-m-d H:i','after:now']
        ];
    }
}
