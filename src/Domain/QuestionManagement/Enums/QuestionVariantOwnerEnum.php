<?php

namespace Domain\QuestionManagement\Enums;

enum QuestionVariantOwnerEnum: string
{
    case Admin = 'admin';

    case Candidate = 'candidate';
}
