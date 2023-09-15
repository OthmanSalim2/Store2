<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    // inside this array name fields.
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
        $this->reportable(function (QueryException $e) {
            if ($e->getCode() == '23000') {
                // here use default channel.
                // Log::warning($e->getMessage());
                // we use custom channel
                Log::channel('sql')->warning($e->getMessage());
                return false;
            }
        });

        // $request laravel passed in this function.
        $this->reportable(function (QueryException $e, Request $request) {
            // 23000 this code for special exception.
            if ($e->getCode() == 23000) {
                $message = 'Foreign Key Constraint Failed';
            } else {
                $message = $e->getMessage();
            }

            if ($request->expectsJson()) {
                // return [
                //     'message' => $e->getMessage(),
                // ];
                return response()->json([
                    'message' => $message,
                ], 400);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'message' => $e->getMessage(),
                ])
                ->with([
                    'info' => $message,
                ]);
        });
    }
}
