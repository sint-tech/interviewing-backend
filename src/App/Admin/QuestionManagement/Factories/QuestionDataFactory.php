<?php

namespace App\Admin\QuestionManagement\Factories;

use App\Admin\QuestionManagement\Requests\QuestionStoreRequest;
use Domain\QuestionManagement\DataTransferObjects\QuestionData;

class QuestionDataFactory
{
    public static function fromRequest(QuestionStoreRequest $request): QuestionData
    {
        return QuestionData::from(
            array_merge(
                $request->validated(),
                [
                    'creator' => auth()->user(),
                ]
            )
        );
    }
}
