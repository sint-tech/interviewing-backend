<?php

namespace App\Organization\InvitationManagement\Factories;

use App\Organization\InvitationManagement\Requests\InvitationStoreRequest;
use Domain\Invitation\DataTransferObjects\InvitationDto;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Http\Request;

class InvitationDtoFactory
{
    public function fromRequest(Request $request): InvitationDto
    {
        return match ($request::class) {
            InvitationStoreRequest::class => $this->fromInvitationStoreRequest($request)
        };
    }

    protected function fromInvitationStoreRequest(InvitationStoreRequest $request): InvitationDto
    {
        $request_data = $request->validated();

        $request_data['dirty_mobile_number'] = $request->validated('mobile_number');

        $request_data['interview_template_id'] = $request->validated(
            'interview_template_id',
            Vacancy::query()->find($request->validated('vacancy_id'))->interview_template_id
        );

        $request_data['creator'] = auth()->user();

        $request_data['expired_at'] = $request->date('expired_at', 'Y-m-d H:i');

        return InvitationDto::from($request_data);
    }
}
