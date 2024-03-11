<?php
namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\Models\Interview;

class ChangeInterviewStatusAction
{
    public function execute(Interview $interview, string $status): Interview
    {
        $interview->update([
            'status' => $status,
        ]);

        return $interview->refresh()->load('candidate');
    }
}
