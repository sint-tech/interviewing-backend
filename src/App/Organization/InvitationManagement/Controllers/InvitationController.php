<?php

namespace App\Organization\InvitationManagement\Controllers;

use App\Organization\InvitationManagement\Factories\InvitationDtoFactory;
use App\Organization\InvitationManagement\Requests\InvitationStoreRequest;
use App\Organization\InvitationManagement\Resources\InvitationResource;
use Domain\Invitation\Actions\CreateInvitationAction;
use Domain\Invitation\Models\Invitation;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class InvitationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return InvitationResource::collection(
            Invitation::query()->paginate(pagination_per_page())
        );
    }

    public function show(int $invitation)
    {
        return InvitationResource::make(
            Invitation::query()->findOrFail($invitation)
        );
    }

    public function store(InvitationStoreRequest $request, CreateInvitationAction $action, InvitationDtoFactory $dtoFactory): InvitationResource
    {
        return InvitationResource::make(
            $action->execute($dtoFactory->fromRequest($request))
        );
    }

    public function update()
    {
        //
    }

    public function destroy(int $invitation): InvitationResource
    {
        $invitation = Invitation::query()->findOrFail($invitation);

        $invitation->delete();

        return InvitationResource::make($invitation);
    }
}
