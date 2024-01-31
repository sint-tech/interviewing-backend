<?php

namespace App\Exceptions;

use Domain\Vacancy\Models\Vacancy;
use Exception;
use Illuminate\Http\JsonResponse;

class TryStartNewInterviewOnTheSameVacancyException extends Exception
{
    /**
     * @throws TryStartNewInterviewOnTheSameVacancyException
     */
    public static function throw(Vacancy $vacancy)
    {
        throw new self(
            sprintf("Can't start new interview on the vacancy %s, as already attended interview before!", $vacancy->title),
        );
    }

    public function render(): JsonResponse
    {
        return message_response($this->message, 409);
    }
}
