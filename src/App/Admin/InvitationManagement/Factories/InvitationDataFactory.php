<?php

namespace App\Admin\InvitationManagement\Factories;

use App\Admin\InvitationManagement\Requests\InvitationStoreRequest;
use Domain\Invitation\DataTransferObjects\InvitationDto;
use Illuminate\Http\Request;
use InvalidArgumentException;

class InvitationDataFactory
{
    public function fromRequest(Request $request): InvitationDto
    {
        if ($request instanceof InvitationStoreRequest) {
            return $this->fromStoreRequest($request);
        }
        throw new InvalidArgumentException('request class: '. $request::class .' not supported!');
    }

    protected function fromStoreRequest(InvitationStoreRequest $request): InvitationDto
    {
        $request_data = $request->validated();

        $request_data['dirty_mobile_number'] = $request->validated('mobile_number');

        return InvitationDto::from($request_data);
    }
}
