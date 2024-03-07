<?php

namespace Domain\QuestionManagement\Builders;

use Domain\Organization\Models\Employee;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\QuestionManagement\Enums\QuestionVariantStatusEnum;

class QuestionVariantBuilder extends Builder
{
    public function forOrganizationEmployee(Employee $employee): self
    {
        return $this->where('organization_id', $employee->organization_id)
            ->orWhere('status', QuestionVariantStatusEnum::Public);
    }

    public function forSintAdmin(User $sintAdmin): self
    {
        return $this;
    }
    public function whereInterviewTemplate(int|InterviewTemplate $interviewTemplate): self
    {
        $interviewTemplateId = $interviewTemplate instanceof InterviewTemplate ? $interviewTemplate->id : $interviewTemplate;

        return $this->whereHas('interviewTemplates', function ($query) use ($interviewTemplateId) {
            $query->where('interview_templates.id', $interviewTemplateId);
        });
    }
}
