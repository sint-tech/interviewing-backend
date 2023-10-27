<?php

namespace Domain\InterviewManagement\Listeners;

use Domain\InterviewManagement\Actions\GenerateInterviewReport;
use Domain\InterviewManagement\Actions\SetInterviewStatusByScoreAction;
use Domain\InterviewManagement\Events\InterviewAllQuestionsAnswered;
use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\ValueObjects\InterviewReportValueObject;

class GenerateInterviewReportListener
{
    public function __construct()
    {

    }

    public function handle(InterviewAllQuestionsAnswered $event)
    {
        $this->generateInterviewReport($event->interview);

        app(SetInterviewStatusByScoreAction::class)->execute($event->interview->refresh());
    }

    private function generateInterviewReport(Interview $interview): InterviewReportValueObject
    {
        return app(GenerateInterviewReport::class)
            ->execute($interview);
    }
}
