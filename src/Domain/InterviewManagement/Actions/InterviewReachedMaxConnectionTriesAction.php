<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\Models\Interview;

class InterviewReachedMaxConnectionTriesAction
{
    public function execute(Interview $interview): bool
    {
        return $interview->connection_tries >= $interview->vacancy()->value('max_reconnection_tries');
    }
}
