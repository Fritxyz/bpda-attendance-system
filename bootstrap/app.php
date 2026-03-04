<?php

use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->redirectGuestsTo(fn () => route('auth.login'));
        // Ito yung pinaka-importante: i-customize kung saan ididirect ang logged-in users
        // kapag sinubukan nilang puntahan ang guest routes (e.g. /signin)
        // $middleware->redirectUsersTo(function ($request) {
        //     $user = $request->user();

        //     if ($user && $user->getRawOriginal('role') === 'Admin') {
        //         return route('admin.dashboard');
        //     }

        //     // Default para sa ibang roles (employee o kung ano pa)
        //     return route('employee.dashboard');
        // });

        // Kung gusto mo ring i-override yung alias ng 'guest' (optional, pero recommended kung may custom class ka)
        $middleware->alias([
            'guest' => RedirectIfAuthenticated::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
