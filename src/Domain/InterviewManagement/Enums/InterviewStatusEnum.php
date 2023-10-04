<?php

namespace Domain\InterviewManagement\Enums;

enum InterviewStatusEnum: string
{
    case Started = 'started';

    //interview's candidate didnt complete the interview, todo: create worker for convert status to withdrew
    case Withdrew = 'withdrew';

    //interview had been canceled
    case Canceled = 'canceled';

    case Passed = 'passed';

    //interview's candidate accepted
    case Accepted = 'accepted';

    //interview's candidate rejected
    case Rejected = 'rejected';

    /**
     * get status refers as this interview had ended and can't be revisited
     * @return array
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
