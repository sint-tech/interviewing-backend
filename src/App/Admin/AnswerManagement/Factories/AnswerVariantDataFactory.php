<?php

namespace App\Admin\AnswerManagement\Factories;

use App\Admin\AnswerManagement\Requests\AnswerVariantStoreRequest;
use Domain\AnswerManagement\DataTransferObjects\AnswerVariantDto;

class AnswerVariantDataFactory
{
    public static function fromStoreRequest(AnswerVariantStoreRequest $request): AnswerVariantDto
    {
        $data = array_merge(
            $request->validated(),
            ['creator' => auth()->user()]
        );

        return AnswerVariantDto::from($data);
    }
}
