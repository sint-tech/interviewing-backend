<?php

namespace Domain\InterviewManagement\Enums;

enum InterviewStatusEnum: string
{
    case Started = 'started';

    //interview's candidate didnt complete the interview, todo: create worker for convert status to withdrew
    case Withdrew = 'withdrew';

    //interview had been canceled
    case Canceled = 'canceled';

    //current user had passed the avg %50
    case Passed = 'passed';

    //interview's candidate rejected
    case Rejected = 'rejected';

    /**
     * get status refers as this interview had ended and can't be revisited
     */
    public static function endedStatuses(): array
    {
        return [
            InterviewStatusEnum::Accepted,
            InterviewStatusEnum::Rejected,
            InterviewStatusEnum::Withdrew,
            InterviewStatusEnum::Canceled,
        ];
    }
}
