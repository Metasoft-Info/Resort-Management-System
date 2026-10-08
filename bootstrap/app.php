<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RunScheduledTasks;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            RunScheduledTasks::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response) {
            if ($response->getStatusCode() === 419 && request()->is('admin/login')) {
                if (request()->expectsJson()) {
                    return response()->json(['message' => 'Your session expired. Please try signing in again.'], 419)
                        ->header('Cache-Control', 'no-store, private');
                }
                return redirect()->to(route('admin.login.form', [], false))
                    ->withInput(request()->only('email'))
                    ->withErrors(['email' => 'Your sign-in session expired. Please enter your password again.']);
            }
            return $response;
        });
    })->create();
