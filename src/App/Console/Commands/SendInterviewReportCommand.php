<?php

namespace App\Console\Commands;

use Domain\InterviewManagement\Actions\SendInterviewReportAction;
use Domain\InterviewManagement\Models\Interview;
use Domain\Vacancy\Builders\VacancyBuilder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class SendInterviewReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:send
                            {vacancy? The vacancy ID}
                            {candidate? The candidate ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send interview report for candidate';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Interview::query()
            ->whereHas('vacancy',function (Builder $builder) {
                return $builder
                    ->when($this->argument('vacancy'), fn(Builder $builder) => $builder->where('id', $this->argument('vacancy')))
                    ->whereEnded();
            })
            ->has('defaultLastReport')
            ->when($this->argument('candidate'), fn(VacancyBuilder $builder) => $builder->where('candidate_id', $this->argument('candidate')))
            ->get()
            ->each(function (Interview $interview) {
                (new SendInterviewReportAction())->execute($interview);
            });
    }
}
