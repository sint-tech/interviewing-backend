<?php

namespace Domain\Invitation\Actions;

use Domain\Invitation\DataTransferObjects\InvitationDto;
use Domain\Invitation\Models\Invitation;

class UpdateInvitationAction
{
    public function execute(Invitation $invitation, InvitationDto $dto): Invitation
    {
        $invitation->update($dto->toArray());

        $invitation->refresh();

        return $invitation;
    }
}
