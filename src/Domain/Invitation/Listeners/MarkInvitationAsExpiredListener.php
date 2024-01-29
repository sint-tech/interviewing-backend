<?php

namespace Domain\Invitation\Listeners;

use Domain\InterviewManagement\Events\InterviewAllQuestionsAnswered;
use Domain\Invitation\Actions\MarkInvitationAsExpiredAction;
use Domain\Invitation\Models\Invitation;
use Support\Scopes\ForAuthScope;

class MarkInvitationAsExpiredListener
{
    public function __construct()
    {
        //
    }

    public function handle(InterviewAllQuestionsAnswered $event)
    {
        Invitation::query()
            ->withoutGlobalScope(ForAuthScope::class)
            ->whereBelongsTo($event->interview->candidate, 'candidate')
            ->where('vacancy_id', $event->interview->vacancy->id)
            ->get()
            ->each(fn (Invitation $invitation) => (new MarkInvitationAsExpiredAction())->execute($invitation));
    }
}
