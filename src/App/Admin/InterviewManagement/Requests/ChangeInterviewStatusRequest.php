<?php

namespace App\Admin\InterviewManagement\Requests;

use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class ChangeInterviewStatusRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'status'    => ['required',new Enum(InterviewStatusEnum::class)]
        ];
    }
}
