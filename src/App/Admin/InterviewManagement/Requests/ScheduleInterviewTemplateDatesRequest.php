<?php

namespace App\Admin\InterviewManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleInterviewTemplateDatesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'started_at' => ['nullable', 'date', 'date_format:Y-m-d H:i', 'after:now'],
            'ended_at' => ['nullable', 'date', 'date_format:Y-m-d H:i', 'after:started_at'],
        ];
    }
}
