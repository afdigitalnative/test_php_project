<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not Found'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {

            return response()->json([
                'status' => 'error',
                'message' => 'Method not allowed'
            ], JsonResponse::HTTP_METHOD_NOT_ALLOWED);
        }

        if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {

            return response()->json([
                'status' => 'error',
                'message' => "You're not authorized to perform this action"
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException) {

            return response()->json([
                'status' => 'error',
                'message' => "You need to be logged in to perform this action"
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof ValidationException) {
            return response()->json([
                'status' => 'error',
                'message' => [
                    'errors' => $exception->getMessage(),
                    'fields' => $exception->validator->getMessageBag()->toArray()
                ]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        return parent::render($request, $exception);
    }
}
