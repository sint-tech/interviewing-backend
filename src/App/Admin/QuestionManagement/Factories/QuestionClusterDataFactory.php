<?php

namespace App\Admin\QuestionManagement\Factories;

use App\Admin\QuestionManagement\Requests\QuestionClusterStoreRequest;
use Domain\QuestionManagement\DataTransferObjects\QuestionClusterDto;

class QuestionClusterDataFactory
{
    public static function fromRequest(QuestionClusterStoreRequest $request): QuestionClusterDto
    {
        $data = $request->validated();

        $data['creator'] = auth()->user();

        return QuestionClusterDto::from($data);
    }
}
