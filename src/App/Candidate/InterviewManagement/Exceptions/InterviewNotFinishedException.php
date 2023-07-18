<?php

namespace App\Candidate\InterviewManagement\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InterviewNotFinishedException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message'   => 'interview not finished yet!'
        ],Response::HTTP_CONFLICT);
    }
}
