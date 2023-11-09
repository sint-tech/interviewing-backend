<?php

namespace App\Admin\InvitationManagement\Controllers;

use App\Admin\InvitationManagement\Factories\InvitationDataFactory;
use App\Admin\InvitationManagement\Queries\InvitationIndexQuery;
use App\Admin\InvitationManagement\Requests\InvitationStoreRequest;
use App\Admin\InvitationManagement\Requests\InvitationUpdateRequest;
use App\Admin\InvitationManagement\Resources\InvitationResource;
use Domain\Invitation\Actions\CreateInvitationAction;
use Domain\Invitation\Actions\UpdateInvitationAction;
use Domain\Invitation\Models\Invitation;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class InvitationController extends Controller
{
    public function index(InvitationIndexQuery $query): AnonymousResourceCollection
    {
        return InvitationResource::collection(
            $query->paginate(
                request()->integer('per_page', 25)
            )
        );
    }

    public function show(int $invitation): InvitationResource
    {

        return InvitationResource::make(
            Invitation::query()->findOrFail($invitation)
        );
    }

    public function store(InvitationStoreRequest $request, InvitationDataFactory $invitationDataFactory, CreateInvitationAction $createInvitationAction): InvitationResource
    {
        $dto = $invitationDataFactory->fromRequest($request);

        return InvitationResource::make(
            $createInvitationAction->execute($dto)->load('vacancy', 'interviewTemplate')
        );
    }

    public function update(
        int $invitation,
        InvitationUpdateRequest $request,
        InvitationDataFactory $invitationDataFactory,
        UpdateInvitationAction $action): InvitationResource
    {
        $dto = $invitationDataFactory->fromRequest($request);

        return InvitationResource::make(
            $action->execute(Invitation::query()->findOrFail($invitation), $dto)->load('vacancy', 'interviewTemplate')
        );
    }

    public function destroy(int $invitation): InvitationResource
    {
        $invitation = Invitation::query()->findOrFail($invitation);

        $invitation->delete();

        return InvitationResource::make($invitation);
    }
}
