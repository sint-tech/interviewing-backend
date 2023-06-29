<?php

namespace App\Website\InterviewManagement\Controllers;

use App\Website\InterviewManagement\Requests\SubmitInterviewQuestionAnswerRequest;
use Domain\InterviewManagement\Models\Interview;
use Support\Controllers\Controller;

class SubmitInterviewQuestionAnswerController extends Controller
{
    public function __invoke(SubmitInterviewQuestionAnswerRequest $request,Interview $interview)
    {

        dd($interview);
    }
}
