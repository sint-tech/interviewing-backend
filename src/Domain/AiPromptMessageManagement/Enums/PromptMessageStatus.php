<?php

namespace Domain\AiPromptMessageManagement\Enums;

enum PromptMessageStatus: string
{
    case Enabled = 'enabled';

    case Disabled = 'disabled';

    case Enabled_not_used = 'enabled_not_used';
}
