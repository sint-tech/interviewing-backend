<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class CannotDeleteSelectedTemplateException extends Exception
{
    public function __construct(string $message = 'Selected prompt template cannot be deleted', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render(Request $request): Response|JsonResponse
    {
        return message_response($this->getMessage(), 422);
    }
}
