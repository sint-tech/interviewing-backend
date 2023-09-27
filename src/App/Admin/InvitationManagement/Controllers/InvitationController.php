<?php

namespace App\Admin\InvitationManagement\Controllers;

use App\Admin\InvitationManagement\Factories\InvitationDataFactory;
use App\Admin\InvitationManagement\Requests\InvitationStoreRequest;
use App\Admin\InvitationManagement\Resources\InvitationResource;
use Domain\Invitation\Actions\CreateInvitationAction;
use Domain\Invitation\Models\Invitation;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class InvitationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return InvitationResource::collection(
            Invitation::query()->latest()->paginate(
                request()->integer('per_page',25)
            )
        );
    }

    public function show(Invitation $invitation): InvitationResource
    {
        return InvitationResource::make($invitation);
    }


    public function store(InvitationStoreRequest $request,InvitationDataFactory $invitationDataFactory,CreateInvitationAction $createInvitationAction): InvitationResource
    {
        $dto = $invitationDataFactory->fromRequest($request);

        return InvitationResource::make(
            $createInvitationAction->execute($dto)
        );
    }

    public function update()
    {
        //
    }

    public function destroy(Invitation $invitation): InvitationResource
    {
        $invitation->delete();

        return InvitationResource::make($invitation);
    }
}
