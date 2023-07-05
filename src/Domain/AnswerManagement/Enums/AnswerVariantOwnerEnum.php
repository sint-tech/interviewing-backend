<?php

namespace Domain\AnswerManagement\Enums;

enum AnswerVariantOwnerEnum: string
{
    case Admin = 'admin';

    case Organization = 'organization';
}
