<?php

namespace App\Organization\Auth\Factories;

use App\Organization\Auth\Requests\RegisterRequest;
use Domain\Organization\DataTransferObjects\EmployeeData;
use Domain\Organization\Models\Organization;
use Illuminate\Http\Request;

class EmployeeDataFactory
{
    protected ?Organization $organization = null;

    public function fromRequest(Request $request)
    {
        if ($request instanceof RegisterRequest) {

            $data = (array) $request->validated('manager');

            $data += [
                'is_organization_manager' => true,
                'organization_id' => $this->organization?->getKey(),
            ];

            return EmployeeData::from($data);
        }
    }
}
