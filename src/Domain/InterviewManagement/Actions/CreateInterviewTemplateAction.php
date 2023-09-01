<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\DataTransferObjects\InterviewTemplateDto;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\InterviewManagement\Models\InterviewTemplateQuestion;

class CreateInterviewTemplateAction
{
    public function __construct(
        public readonly InterviewTemplateDto $interviewTemplateDto,
    ) {

    }

    public function execute(): InterviewTemplate
    {
        $interviewTemplate = (new InterviewTemplate())->fill(
            $this->interviewTemplateDto->toArray()
        );

        $interviewTemplate->save();

        foreach ($this->interviewTemplateDto->question_variants as $question_variant) {
            InterviewTemplateQuestion::query()->create([
                'question_variant_id' => $question_variant->getKey(),
                'interview_template_id' => $interviewTemplate->getKey(),
                'question_cluster_id' => $question_variant->questionCluster?->getKey(),
            ]);
        }

        return $interviewTemplate->refresh();
    }
}
