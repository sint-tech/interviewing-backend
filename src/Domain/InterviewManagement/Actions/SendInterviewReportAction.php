<?php

namespace Domain\InterviewManagement\Actions;

use App\Mail\Candidate\InterviewReportMail;
use Domain\InterviewManagement\Models\Interview;
use Illuminate\Support\Facades\Mail;

class SendInterviewReportAction
{
    /**
     * @throws \Exception
     */
    public function execute(Interview $interview)
    {
        if ($interview->candidate_report_sent_at) {
            throw new \Exception('candidate report sent before');
        }

        Mail::send(new InterviewReportMail($interview));

        $interview->update([
            'candidate_report_sent_at' => now()
        ]);
    }
}
