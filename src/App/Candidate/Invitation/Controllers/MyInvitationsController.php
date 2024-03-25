<?php

namespace App\Candidate\Invitation\Controllers;

use App\Candidate\Invitation\Resources\InvitationResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Support\Controllers\Controller;
use App\Candidate\Invitation\Queries\MyInvitationsQuery;

class MyInvitationsController extends Controller
{
    public function __invoke(MyInvitationsQuery $query): AnonymousResourceCollection
    {
        return InvitationResource::collection(
            $query
            ->with('vacancy.organization')
            ->paginate(pagination_per_page())
        );
    }
}
