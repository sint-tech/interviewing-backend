<?php

namespace Domain\Invitation\Actions;

use Carbon\Carbon;
use Domain\Invitation\Models\Invitation;

class MarkInvitationAsExpiredAction
{
    public function execute(Invitation $invitation, string|\DateTime|Carbon $expired_at = null): bool
    {
        return $invitation->update([
            'expired_at' => $expired_at ?? now(),
        ]);
    }
}
