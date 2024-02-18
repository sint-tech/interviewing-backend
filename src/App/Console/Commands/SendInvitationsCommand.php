<?php

namespace App\Console\Commands;

use Domain\Invitation\Actions\SendInvitationAction;
use Domain\Invitation\Models\Invitation;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class SendInvitationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invitations:send
                            {vacancy? : The ID of the vacancy}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "send interview's invitations for candidates";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Invitation::query()
            ->when(
                $vacancy_id = $this->argument('vacancy'),
                fn (Builder $builder) => $builder->where('vacancy_id', $vacancy_id)
            )
            ->where(function (Builder $builder) {
                return $builder
                    ->whereDate('expired_at', '<=', now())
                    ->orWhereNull('expired_at');
            })
            ->whereNull('last_invited_at')
            ->where('should_be_invited_at', '>=', now())
            ->orderByDesc('created_at')
            ->cursor()
            ->each(function (Invitation $invitation) {
                try {
                    (new SendInvitationAction)->execute($invitation);
                } catch (\Exception $exception) {
                    logger()->error($exception->getMessage());
                }
            });
    }
}
