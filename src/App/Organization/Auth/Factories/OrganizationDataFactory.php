<?php

namespace App\Organization\Auth\Factories;

use App\Organization\Auth\Requests\RegisterRequest;
use Domain\Organization\DataTransferObjects\OrganizationData;
use Illuminate\Http\Request;

class OrganizationDataFactory
{
    public function fromRequest(Request $request): OrganizationData
    {
        if ($request instanceof RegisterRequest) {
            $data = $request->only([
                'name',
                'logo',
            ]);

            return OrganizationData::from($data);
        }
    }
}
