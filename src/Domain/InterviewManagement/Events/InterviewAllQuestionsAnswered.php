<?php

namespace Domain\InterviewManagement\Events;

use Domain\InterviewManagement\Models\Interview;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InterviewAllQuestionsAnswered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Interview $interview
    ) {

    }
}
