<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ModelHasRelationsPreventDeleteException extends Exception
{
    /**
     * @throws ModelHasRelationsPreventDeleteException
     */
    public static function make(Model $model, string|array $relations)
    {
        $relations = collect($relations)->join(', ', ',And ');

        $errMessage = sprintf('model with %s: %s can\'t be deleted as it has these relations: %s', $model->getKeyName(), $model->getKey(), $relations);

        throw new self($errMessage);
    }


    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request):JsonResponse
    {
        return message_response($this->message, 409);
    }
}
