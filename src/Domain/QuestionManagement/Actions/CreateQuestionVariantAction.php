<?php

namespace Domain\QuestionManagement\Actions;

use Domain\AiPromptMessageManagement\Models\AIModel;
use Domain\QuestionManagement\DataTransferObjects\QuestionVariantDto;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Support\Arr;

class CreateQuestionVariantAction
{
    public function __construct(
        public QuestionVariantDto $questionVariantDto
    ) {
    }

    public function execute(): QuestionVariant
    {
        $question_variant = new QuestionVariant();

        $data = $this->questionVariantDto->except('creator','owner')->toArray();

        $question_variant->fill($data)->save();

        $question_variant = $question_variant->refresh();

        $this->syncQuestionVariantWithAIModelIds($question_variant);

        return $question_variant->load([
            'question',
            'owner',
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
                'is_default' => $index === 0
            ];
        }

        $questionVariant->aiModels()->sync($ai_model_value);
    }
}
