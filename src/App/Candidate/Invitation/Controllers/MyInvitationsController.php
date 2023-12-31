<?php

namespace App\Candidate\Invitation\Controllers;

use App\Candidate\Invitation\Resources\InvitationResource;
use Domain\Invitation\Models\Invitation;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;

class MyInvitationsController extends Controller
{
    public function __invoke(): AnonymousResourceCollection
    {
        return InvitationResource::collection(
            Invitation::query()->paginate(pagination_per_page())
        );
    }
}
