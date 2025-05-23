<?php

namespace App\Admin\QuestionManagement\Factories;

use App\Admin\QuestionManagement\Requests\QuestionVariantStoreRequest;
use App\Admin\QuestionManagement\Requests\QuestionVariantUpdateRequest;
use Domain\QuestionManagement\DataTransferObjects\QuestionVariantDto;

class QuestionVariantDataFactory
{
    public static function fromRequest(QuestionVariantStoreRequest $request): QuestionVariantDto
    {
        $data = array_merge($request->validated(), [
            'creator' => auth()->user(),
        ]);

        return QuestionVariantDto::from($data);
    }

    public static function fromUpdateRequest(QuestionVariantUpdateRequest $request): QuestionVariantDto
    {
        $data = array_merge($request->questionVariant()->toArray(), $request->validated());

        return QuestionVariantDto::from($data);
    }
}
