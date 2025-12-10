<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed for validation exceptions.
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

    /**
     * Render an exception into a response.
     */
    public function render($request, Throwable $exception)
    {
        // Handle ResourceNotFoundException
        if ($exception instanceof ResourceNotFoundException) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], 404);
        }

        // Handle ValidationException
        if ($exception instanceof ValidationException) {
            return response()->json([
                'errors' => json_decode($exception->getMessage(), true),
            ], 422);
        }

        return parent::render($request, $exception);
    }
}
