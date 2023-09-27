<?php

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

if(! function_exists('message_response')) {

    function message_response(string $message, int $status = ResponseAlias::HTTP_OK, string $messageKey = 'message', array $appended = []): JsonResponse
    {
        $data = array_merge($appended,[$messageKey => $message]);

        return response()
            ->json($data,$status);
    }
}
