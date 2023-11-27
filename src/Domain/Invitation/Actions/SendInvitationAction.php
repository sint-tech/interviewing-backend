<?php

namespace Domain\Invitation\Actions;

use App\Mail\InterviewInvitation;
use Domain\Invitation\Models\Invitation;
use Illuminate\Support\Facades\Mail;

class SendInvitationAction
{
    /**
     * @throws \Exception
     */
    public function execute(Invitation $invitation): Invitation
    {
        if ($invitation->sent) {
            //todo throw exception
            throw new \Exception('invitation already had been sent');
        }

        Mail::send(new InterviewInvitation($invitation));

        $invitation->update([
            'last_invited_at' => now(),
        ]);

        return $invitation->refresh();
    }
}
