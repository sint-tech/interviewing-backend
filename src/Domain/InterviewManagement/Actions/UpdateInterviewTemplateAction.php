<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\DataTransferObjects\InterviewTemplateDto;
use Domain\InterviewManagement\Models\InterviewTemplate;

class UpdateInterviewTemplateAction
{
    public function execute(
        InterviewTemplate $interviewTemplate,
        InterviewTemplateDto $interviewTemplateDto
    ): InterviewTemplate {
        $interviewTemplateDto->except('question_variants', 'creator');

        $interviewTemplate->update($interviewTemplateDto->toArray());

        $interviewTemplate->questionVariants()->sync([]);

        $question_variants = [];

        foreach ($interviewTemplateDto->question_variants as $question_variant) {
            $question_variants[$question_variant->getKey()] = [
                'question_cluster_id' => $question_variant->questionCluster?->getKey(),
            ];
        }

        $interviewTemplate->questionVariants()->sync($question_variants);

        return $interviewTemplate->refresh()->load('questionVariants');
    }
}
