<?php

namespace Domain\InterviewManagement\Listeners;

use Domain\InterviewManagement\Actions\GenerateInterviewReport;
use Domain\InterviewManagement\Events\InterviewAllQuestionsAnswered;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateInterviewReportListener
{
    public function __construct()
    {

    }

    public function handle(InterviewAllQuestionsAnswered $event)
    {
        app(GenerateInterviewReport::class)
            ->execute($event->interview);
    }
}
