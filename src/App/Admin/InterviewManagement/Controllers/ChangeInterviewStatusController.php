<?php

namespace App\Admin\InterviewManagement\Controllers;

use App\Admin\InterviewManagement\Requests\ChangeInterviewStatusRequest;
use App\Admin\InterviewManagement\Resources\InterviewResource;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\Models\Interview;
use Support\Controllers\Controller;

class ChangeInterviewStatusController extends Controller
{
    public function __invoke(Interview $interview,ChangeInterviewStatusRequest $request): InterviewResource
    {
        //todo create action class
        if ($interview->statusIs(InterviewStatusEnum::Finished)) {
            throw new \LogicException('you can not change interview status as its already had been ended!');
        }

        $interview->update([
            'status' => $request->validated('status')
        ]);

        return InterviewResource::make(
            $interview->refresh()->load('candidate')
        );
    }
}
