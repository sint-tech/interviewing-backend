<?php

namespace App\Admin\InterviewManagement\Controllers;

use App\Admin\InterviewManagement\Requests\ChangeInterviewStatusRequest;
use App\Admin\InterviewManagement\Resources\InterviewResource;
use Domain\InterviewManagement\Actions\ChangeInterviewStatusAction;
use Domain\InterviewManagement\Models\Interview;
use Support\Controllers\Controller;

class ChangeInterviewStatusController extends Controller
{
    public function __invoke(Interview $interview, ChangeInterviewStatusRequest $request, ChangeInterviewStatusAction $action): InterviewResource
    {
        return InterviewResource::make(
            $action->execute($interview, $request->validated('status'))
        );
    }
}
