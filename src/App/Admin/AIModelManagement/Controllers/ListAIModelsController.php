<?php

namespace App\Admin\AIModelManagement\Controllers;

use App\Admin\AIModelManagement\Queries\IndexAIModelQuery;
use App\Admin\AIModelManagement\Resources\AIModelResource;
use Domain\AiPromptMessageManagement\Enums\AiModelEnum;
use Support\Controllers\Controller;

class ListAIModelsController extends Controller
{
    public function __invoke(): array
    {
        return AiModelEnum::cases();
    }

}
