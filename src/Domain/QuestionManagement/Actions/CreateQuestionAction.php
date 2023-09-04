<?php

namespace Domain\QuestionManagement\Actions;

use Domain\AiPromptMessageManagement\Models\AIModel;
use Domain\QuestionManagement\DataTransferObjects\QuestionData;
use Domain\QuestionManagement\Models\Question;
use Spatie\LaravelData\Optional;

class CreateQuestionAction
{
    public function __construct(
        public readonly QuestionData $questionData
    ) {

    }

    public function execute(): Question
    {
        $data = $this->questionData->except('creator')->toArray();

        if ($this->questionData->default_ai_model_id instanceof Optional) {
            $data['default_ai_model_id'] = AIModel::query()->first()->getKey();
        }

        $question = new Question($data);

        $question->save();

        return $question->load(['creator', 'questionCluster','questionVariants']);
    }
}
