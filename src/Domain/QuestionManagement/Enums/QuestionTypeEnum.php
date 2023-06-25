<?php

namespace Domain\QuestionManagement\Enums;

enum QuestionTypeEnum: string
{
    case Written = 'written';

    case Boolean = 'boolean';

    case Mcq = 'mcq';
}
