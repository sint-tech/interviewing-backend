<?php

namespace App\Admin\QuestionManagement\Factories;

use App\Admin\QuestionManagement\Requests\QuestionStoreRequest;
use App\Admin\QuestionManagement\Requests\QuestionUpdateRequest;
use Domain\QuestionManagement\DataTransferObjects\QuestionData;
use Spatie\LaravelData\Optional;

class QuestionDataFactory
{
    public static function fromStoreRequest(QuestionStoreRequest $request): QuestionData
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

    public static function fromUpdateRequest(QuestionUpdateRequest $request): QuestionData
    {
        return QuestionData::from(
            array_merge(
                $request->validated(),
                [
                    'min_reading_duration_in_seconds' => $request->input('min_reading_duration_in_seconds', Optional::create()),
                    'max_reading_duration_in_seconds' => $request->input('max_reading_duration_in_seconds', Optional::create()),
                ]
            )
        );
    }
}
