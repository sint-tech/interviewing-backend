<?php

namespace Domain\QuestionManagement\Enums;

use Support\Traits\EnumToArray;

enum QuestionVariantStatusEnum: int
{
    use EnumToArray;
    case Private = 1;
    case Public = 2;
}
