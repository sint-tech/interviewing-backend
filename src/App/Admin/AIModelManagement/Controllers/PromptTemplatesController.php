<?php

namespace App\Admin\AIModelManagement\Controllers;

use Support\Controllers\Controller;
use Domain\AiPromptMessageManagement\Models\PromptTemplate;
use App\Admin\AIModelManagement\Resources\PromptTemplateResource;
use App\Admin\AIModelManagement\Requests\PromptTemplateStoreRequest;
use App\Admin\AIModelManagement\Requests\PromptTemplateUpdateRequest;
use Domain\AiPromptMessageManagement\Actions\CreatePromptTemplateAction;
use Domain\AiPromptMessageManagement\Actions\DeletePromptTemplateAction;
use Domain\AiPromptMessageManagement\Actions\UpdatePromptTemplateAction;
use Domain\AiPromptMessageManagement\DataTransferObjects\PromptTemplateDto;

class PromptTemplatesController extends Controller
{
    public function index()
    {
        return PromptTemplateResource::collection(PromptTemplate::all());
    }

    public function store(PromptTemplateStoreRequest $request, CreatePromptTemplateAction $createPromptTemplateAction)
    {
        return PromptTemplateResource::make(
            $createPromptTemplateAction->execute(PromptTemplateDto::from($request->validated()))
        );
    }

    public function update(PromptTemplateUpdateRequest $request, PromptTemplate $promptTemplate)
    {
        return PromptTemplateResource::make(
            (new UpdatePromptTemplateAction($promptTemplate, PromptTemplateDto::from($request->validated())))->execute()
        );
    }

    public function destroy(PromptTemplate $promptTemplate, DeletePromptTemplateAction $deletePromptTemplateAction)
    {
        return PromptTemplateResource::make(
            $deletePromptTemplateAction->execute($promptTemplate)
        );
    }
}
