<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
    protected $dontFlash = ['password', 'password_confirmation'];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    #[\Override]
    public function register()
    {
        /** @psalm-suppress UnusedClosureParam Empty scaffolding hook for future custom
         * exception reporting - $e is intentionally unused for now. */
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
