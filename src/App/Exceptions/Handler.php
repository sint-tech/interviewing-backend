<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if (app()->isLocal()) {
            return parent::render($request, $e);
        }

        if ($e instanceof ModelNotFoundException) {
            $ids = Arr::join($e->getIds(), ', ', 'and ');

            return message_response("no query result for $ids", Response::HTTP_NOT_FOUND);
        }

        if ($e instanceof NotFoundHttpException) {
            return message_response(sprintf('URL:%s not found', url()->current()), 404);
        }

        if ($e instanceof AuthorizationException) {
            return message_response($e->getMessage(), $e->status() ?? 403);
        }

        return parent::render($request, $e);
    }
}
