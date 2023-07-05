<?php

namespace Domain\QuestionManagement\Enums;

use Support\Traits\EnumToArray;

enum QuestionVariantOwnerEnum: string
{
    use EnumToArray;

    case Admin = 'admin';

    case Organization = 'organization';
}
