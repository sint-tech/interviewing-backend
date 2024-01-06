<?php

namespace App\Exceptions;

use Domain\InterviewManagement\Models\Interview;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class InterviewReachedMaxConnectionTriesException extends Exception
{
    protected Interview $interview;

    public function __construct(string $message = 'Interview reached max connection tries', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function interview(Interview $interview)
    {
        $this->interview = $interview;
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): Response|JsonResponse
    {
        if ($request->route()->named('candidate.interviews.start')) {
            return message_response($this->getMessage(), 422);
        }

        return message_response($this->getMessage(), 500);
    }
}
