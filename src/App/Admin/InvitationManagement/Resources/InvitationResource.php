<?php

namespace App\Admin\InvitationManagement\Resources;

use App\Admin\InterviewManagement\Resources\InterviewTemplateResource;
use App\Admin\Vacancy\Resources\VacancyResource;
use Domain\Invitation\Models\Invitation;
use Illuminate\Http\Resources\Json\JsonResource;
use Support\ValueObjects\DateToHumanReadValueObject;

/**
 * @property Invitation $resource
 */
class InvitationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->resource->getKey(),
            'name' => (string) $this->name,
            'batch' => (int) $this->batch,
            'email' => (string) $this->email,
            'mobile_number' => (int) $this->mobile_number,
            'mobile_country_code' => (string) $this->mobile_country_code,
            'should_be_invited_at' => (string) new DateToHumanReadValueObject($this->should_be_invited_at),
            'vacancy' => VacancyResource::make($this->whenLoaded('vacancy')),
            'interview_template' => InterviewTemplateResource::make($this->whenLoaded('interviewTemplate')),
            'invitation_sent' => $this->resource->invitationSent(),
            'created_at' => (string) new DateToHumanReadValueObject($this->resource->created_at),
            'expired_at' => DateToHumanReadValueObject::format($this->resource->expired_at),
        ];
    }
}
