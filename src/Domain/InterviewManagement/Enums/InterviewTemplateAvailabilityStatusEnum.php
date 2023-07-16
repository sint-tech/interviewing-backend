<?php

namespace Domain\InterviewManagement\Enums;

enum InterviewTemplateAvailabilityStatusEnum:string
{
    case Pending = 'pending';

    case Available = 'available';

    case unAvailable = 'unavailable';

    case Paused = 'paused';
}
