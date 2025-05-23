<?php

namespace Domain\Invitation\Actions;

use Domain\Invitation\DataTransferObjects\InvitationDto;
use Domain\Invitation\Models\Invitation;

class CreateInvitationAction
{
    public function execute(InvitationDto $invitationDto): Invitation
    {
        $invitation = new Invitation($invitationDto->except('creator')->toArray());

        $invitation->save();

        $invitation->vacancy->organization->increment('interview_consumption');

        return $invitation->refresh();
    }
}
