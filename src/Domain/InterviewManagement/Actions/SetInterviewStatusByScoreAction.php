<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\Models\Interview;

class SetInterviewStatusByScoreAction
{
    /**
     * @throws \Exception
     */
    public function execute(Interview $interview): Interview
    {
        if ($interview->running()) {
            throw new \Exception('interview should be ended to set its status');
        }

        if ($interview->defaultLastReport()->doesntExist()) {
            throw new \Exception('interview default report should be generated');
        }

        $interview->update([
            'status' => $this->getInterviewStatus($interview),
        ]);

        return $interview->refresh();
    }

    protected function getInterviewStatus(Interview $interview): InterviewStatusEnum
    {
        if ($interview->defaultLastReport->getMeta()->toArray()['avg_score'] < 50) {
            return InterviewStatusEnum::Rejected;
        }

        return InterviewStatusEnum::Passed;
    }
}
