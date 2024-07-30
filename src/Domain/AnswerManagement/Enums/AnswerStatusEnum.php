<?php

namespace Domain\AnswerManagement\Enums;

enum AnswerStatusEnum: string
{
    case NotSent = 'not_sent';
    case Successful = 'successful';
    case Failed = 'failed';
}
