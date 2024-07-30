<?php

namespace App\Candidate\Invitation\Controllers;

use Domain\Invitation\Actions\CreateInvitationAction;
use App\Candidate\Invitation\Resources\InvitationResource;
use App\Candidate\Invitation\Requests\ExternalInviteRequest;
use App\Organization\InvitationManagement\Factories\InvitationDtoFactory;


class ExternalInviteController
{
    public function __invoke(ExternalInviteRequest $request, CreateInvitationAction $action, InvitationDtoFactory $dtoFactory)
    {
        return InvitationResource::make(
            $action->execute($dtoFactory->fromRequest($request))->load('vacancy')
        );
    }
}
