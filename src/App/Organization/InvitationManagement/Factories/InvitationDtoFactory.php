<?php

namespace App\Organization\InvitationManagement\Factories;

use App\Candidate\Invitation\Requests\ExternalInviteRequest;
use App\Organization\InvitationManagement\Requests\InvitationStoreRequest;
use Domain\Invitation\DataTransferObjects\InvitationDto;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class InvitationDtoFactory
{
    public function fromRequest(Request $request): InvitationDto
    {
        return match ($request::class) {
            InvitationStoreRequest::class => $this->fromInvitationStoreRequest($request),
            ExternalInviteRequest::class => $this->fromExternalInviteRequest($request),
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

    protected function fromExternalInviteRequest(ExternalInviteRequest $request): InvitationDto
    {
        $request_data = $request->validated();

        $request_data['creator'] = $request->vacancy()->creator;

        $request_data['interview_template_id'] = $request->validated(
            'interview_template_id',
            $request->vacancy()->interview_template_id
        );

        $request_data['should_be_invited_at'] = Carbon::parse($request->vacancy()->started_at);

        $request_data['is_external'] = true;

        return InvitationDto::from($request_data);
    }
}
