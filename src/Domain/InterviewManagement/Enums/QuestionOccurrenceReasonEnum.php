<?php

namespace Domain\InterviewManagement\Enums;

enum QuestionOccurrenceReasonEnum: string
{
    case TemplateQuestion = 'template_question';

    case Additional = 'additional';

    case Recommended = 'recommended';
}
