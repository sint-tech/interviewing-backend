<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\DataTransferObjects\InterviewTemplateDto;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\InterviewManagement\Models\InterviewTemplateQuestion;
use Domain\QuestionManagement\Models\QuestionVariant;

class UpdateInterviewTemplateAction
{
    public function execute(
        InterviewTemplate $interviewTemplate,
        InterviewTemplateDto $interviewTemplateDto
    ): InterviewTemplate
    {
        $interviewTemplateDto->except('question_variants','owner','creator','interview_template_settings_dto');

        $interviewTemplate->update($interviewTemplateDto->toArray());

        foreach ($interviewTemplateDto->question_variants as $question_variant) {
            InterviewTemplateQuestion::query()->updateOrCreate([
                'question_variant_id' => $question_variant->getKey(),
                'interview_template_id' => $interviewTemplate->getKey(),
                'question_cluster_id' => $question_variant->questionCluster?->getKey(),
            ]);
        }

        return $interviewTemplate->refresh()->load('questionVariants');
    }
}
