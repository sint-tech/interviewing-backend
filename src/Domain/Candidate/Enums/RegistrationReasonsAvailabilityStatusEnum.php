<?php

namespace Domain\Candidate\Enums;

enum RegistrationReasonsAvailabilityStatusEnum: string
{
    case Active = 'active';

    case Inactive = 'inactive';
}
