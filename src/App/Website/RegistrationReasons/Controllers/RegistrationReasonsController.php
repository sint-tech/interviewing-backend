<?php

namespace App\Website\RegistrationReasons\Controllers;

use App\Website\RegistrationReasons\Queries\IndexRegistrationReasonQuery;
use App\Website\RegistrationReasons\Resources\RegistrationReasonResource;
use Domain\Candidate\Models\RegistrationReason;
use Support\Controllers\Controller;

class RegistrationReasonsController extends Controller
{
    public function index(IndexRegistrationReasonQuery $query)
    {
        $data = $query->paginate(request('per_page',25));

        return RegistrationReasonResource::collection($data);
    }

    public function show(int $registrationReasonId)
    {
        $registrationReason = RegistrationReason::query()->findOrFail($registrationReasonId);

        return RegistrationReasonResource::make($registrationReason);
    }
}
