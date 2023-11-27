<?php

namespace App\Admin\InvitationManagement\Controllers;

use App\Admin\InvitationManagement\Resources\InvitationResource;
use Domain\Invitation\Actions\SendInvitationAction;
use Domain\Invitation\Models\Invitation;
use Support\Controllers\Controller;

class SendInvitationController extends Controller
{
    public function __invoke(int $invitation, SendInvitationAction $action): InvitationResource
    {
        return InvitationResource::make(
            $action->execute(Invitation::query()->whereNull('last_invited_at')->findOrFail($invitation))
        );
    }
}
