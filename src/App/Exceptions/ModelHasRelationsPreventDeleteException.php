<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ModelHasRelationsPreventDeleteException extends Exception
{
    /**
     * @throws ModelHasRelationsPreventDeleteException
     */
    public static function make(Model $model, string|array $relations)
    {
        $relations = self::generateRelationNames($relations);

        $errMessage = sprintf('%s can\'t be deleted as it has %s', self::generateClassName($model), $relations);

        throw new self($errMessage);
    }

    private static function generateClassName(Model $model): string
    {
        $className = class_basename($model);
        $className = Str::of($className)->kebab()->replace('-', ' ');
        return $className;
    }

    private static function generateRelationNames(array|string $relations): string
    {
        return collect($relations)
            ->map(function ($relation) {
                return Str::of($relation)->kebab()->replace('-', ' ');
            })
            ->join(', ', ', and ');
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request):JsonResponse
    {
        return message_response($this->message, 409);
    }
}
