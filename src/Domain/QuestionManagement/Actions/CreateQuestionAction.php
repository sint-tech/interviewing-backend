<?php

namespace Domain\QuestionManagement\Actions;

use Domain\AiPromptMessageManagement\Models\AIModel;
use Domain\QuestionManagement\DataTransferObjects\QuestionData;
use Domain\QuestionManagement\Models\Question;
use Spatie\LaravelData\Optional;

class CreateQuestionAction
{
    public function __construct(
    ) {

    }

    public function execute(QuestionData $questionData): Question
    {
        $data = $questionData->except('creator')->toArray();

        if ($questionData->default_ai_model_id instanceof Optional) {
            $data['default_ai_model_id'] = AIModel::query()->first()->getKey();
        }

        $question = new Question($data);

        $question->save();

        return $question->load(['creator', 'questionCluster', 'questionVariants']);
    }
}
