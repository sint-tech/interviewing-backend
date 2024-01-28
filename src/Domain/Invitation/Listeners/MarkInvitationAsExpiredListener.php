<?php

namespace Domain\Invitation\Listeners;

use Domain\InterviewManagement\Events\InterviewAllQuestionsAnswered;
use Domain\Invitation\Actions\MarkInvitationAsExpiredAction;
use Domain\Invitation\Models\Invitation;
use Illuminate\Database\Eloquent\Builder;
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
            ->where(
                function (Builder $builder) use ($event) {
                    return $builder
                        ->where('vacancy_id', $event->interview->vacancy->id)
                        ->where('interview_template_id', $event->interview->interviewTemplate->id);
                })->get()
            ->each(fn (Invitation $invitation) => (new MarkInvitationAsExpiredAction())->execute($invitation));
    }
}
