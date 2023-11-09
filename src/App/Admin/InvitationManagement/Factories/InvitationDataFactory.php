<?php

namespace App\Admin\InvitationManagement\Factories;

use App\Admin\InvitationManagement\Requests\InvitationStoreRequest;
use App\Admin\InvitationManagement\Requests\InvitationUpdateRequest;
use Domain\Invitation\DataTransferObjects\InvitationDto;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Http\Request;

class InvitationDataFactory
{
    public function fromRequest(Request $request): InvitationDto
    {
        return match (get_class($request)) {
            InvitationStoreRequest::class => $this->fromStoreRequest($request),
            InvitationUpdateRequest::class => $this->fromUpdateRequest($request),
        };
    }

    protected function fromStoreRequest(InvitationStoreRequest $request): InvitationDto
    {
        $request_data = $request->validated();

        $request_data['dirty_mobile_number'] = $request->validated('mobile_number');

        $request_data['interview_template_id'] = $request->validated(
            'interview_template_id',
            Vacancy::query()->find($request->validated('vacancy_id'))->interview_template_id
        );

        $request_data['creator'] = auth()->user();

        $request['expired_at'] = $request->date('expired_at');

        return InvitationDto::from($request_data);
    }

    protected function fromUpdateRequest(InvitationUpdateRequest $request): InvitationDto
    {
        $invitation = $request->invitation()->load('creator');

        $request_data = $request->validated();

        $request_data['dirty_mobile_number'] = $request->validated('mobile_number', $invitation->mobile_number);

        if ($request->filled('vacancy_id') && $request->isNotFilled('interview_template_id')) {
            $request_data['interview_template_id'] = Vacancy::query()->find($request->validated('vacancy_id'))->interview_template_id;
        }

        return InvitationDto::from(
            array_merge($invitation->toArray(), $request_data)
        );
    }
}
