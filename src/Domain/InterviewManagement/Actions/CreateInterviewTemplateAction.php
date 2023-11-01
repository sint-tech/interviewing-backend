<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\DataTransferObjects\InterviewTemplateDto;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\InterviewManagement\Models\InterviewTemplateQuestion;

class CreateInterviewTemplateAction
{
    public function __construct(
    ) {
    }

    public function execute(InterviewTemplateDto $interviewTemplateDto): InterviewTemplate
    {
        $interviewTemplate = (new InterviewTemplate())->fill(
            $interviewTemplateDto->except('creator', 'owner')->toArray()
        );

        $interviewTemplate->save();

        $interviewTemplate = $interviewTemplate->refresh();

        foreach ($interviewTemplateDto->question_variants as $question_variant) {
            InterviewTemplateQuestion::query()->create([
                'question_variant_id' => $question_variant->getKey(),
                'interview_template_id' => $interviewTemplate->getKey(),
                'question_cluster_id' => $question_variant->questionCluster?->getKey(),
            ]);
        }

        return $interviewTemplate;
    }
}
