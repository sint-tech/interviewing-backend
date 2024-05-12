<?php

namespace App\Exceptions;

use Exception;

class LimitExceededException extends Exception
{
    public function __construct(string $message = 'Limit exceeded', int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render()
    {
        return message_response($this->getMessage(), 422);
    }
}
