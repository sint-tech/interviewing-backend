<?php

namespace Domain\QuestionManagement\Actions;

use Domain\QuestionManagement\DataTransferObjects\QuestionVariantDto;
use Domain\QuestionManagement\Models\QuestionVariant;

class CreateQuestionVariantAction
{
    protected QuestionVariantDto $questionVariantDto;

    public function __construct(
    ) {
    }

    public function execute(QuestionVariantDto $questionVariantDto): QuestionVariant
    {
        $this->questionVariantDto = $questionVariantDto;

        $question_variant = new QuestionVariant();

        $data = $this->questionVariantDto->except('creator', 'owner', 'ai_prompts')->toArray();

        $question_variant->fill($data)->save();

        $question_variant = $question_variant->refresh();

        $question_variant
            ->aiPrompts()
            ->createMany($questionVariantDto->ai_prompts);

        return $question_variant->load([
            'question',
            'creator',
        ]);
    }

    private function syncQuestionVariantWithAIModelIds(QuestionVariant $questionVariant): void
    {
        $ai_model_ids = is_array($this->questionVariantDto->ai_model_ids) && count($this->questionVariantDto->ai_model_ids) ?
            $this->questionVariantDto->ai_model_ids : [$questionVariant->question->defaultAiModel->getKey()];

        $ai_model_value = [];

        foreach ($ai_model_ids as $index => $ai_model_id) {
            $ai_model_value[$ai_model_id] = [
                'prompt_text' => '',
                'is_default' => $index === 0,
            ];
        }

        $questionVariant->aiModels()->sync($ai_model_value);
    }
}
