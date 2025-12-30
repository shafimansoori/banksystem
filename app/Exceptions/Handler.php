<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Handle CSRF token mismatch (419)
        if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            // If it's a POST/PUT/DELETE request, redirect back with error
            if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('delete')) {
                return redirect()->back()
                    ->withInput($request->except(['password', 'password_confirmation', '_token']))
                    ->with('error', 'Oturumunuzun süresi doldu. Lütfen tekrar deneyin.');
            }

            // For GET requests, redirect to login
            return redirect()->route('login')
                ->with('error', 'Oturumunuzun süresi doldu. Lütfen tekrar giriş yapın.');
        }

        return parent::render($request, $exception);
    }
}
