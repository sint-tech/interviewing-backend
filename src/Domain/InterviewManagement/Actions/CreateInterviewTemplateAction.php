<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\DataTransferObjects\InterviewTemplateDto;
use Domain\InterviewManagement\Models\InterviewTemplate;

class CreateInterviewTemplateAction
{
    public function __construct(
    ) {
    }

    public function execute(InterviewTemplateDto $interviewTemplateDto): InterviewTemplate
    {
        $interviewTemplate = (new InterviewTemplate())->fill(
            $interviewTemplateDto->except('creator')->toArray()
        );

        $interviewTemplate->save();

        $interviewTemplate = $interviewTemplate->refresh();

        $question_variants = [];

        foreach ($interviewTemplateDto->question_variants as $question_variant) {
            $question_variants[$question_variant->getKey()] = ['question_cluster_id' => $question_variant->questionCluster?->getKey()];
        }

        $interviewTemplate->questionVariants()->sync($question_variants);

        return $interviewTemplate;
    }
}
