<?php

namespace App\Console\Commands;

use App\Mail\Candidate\CandidateRejectedMail;
use Domain\InterviewManagement\Models\Interview;
use Domain\Vacancy\Builders\VacancyBuilder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class SendRejectionsCommand extends Command
{
    protected $signature = 'rejections:send
                            {vacancy? : The ID of the vacancy}
                            {candidate? : The candidate ID}';

    protected $description = 'Send rejection mail to candidates not passed the vacancy interview.';

    public function handle(): void
    {
        Interview::query()
            ->whereHas('vacancy', function (Builder $builder) {
                return $builder
                    ->when($this->argument('vacancy'), fn (Builder $builder) => $builder->where('id', $this->argument('vacancy')))
                    ->whereEnded();
            })
            ->when($this->argument('candidate'), fn (VacancyBuilder $builder) => $builder->where('candidate_id', $this->argument('candidate')))
            ->get()
            ->each(function (Interview $interview) {
                if ($interview->candidate_report_sent_at) {
                    return;
                }
                Mail::send(new CandidateRejectedMail($interview->id));
            });
    }
}
