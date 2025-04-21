<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) { // Find this section
        // Add your middleware alias here
        $middleware->alias([ // Chain the alias() method
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            // Add other aliases if needed, e.g., 'verified' is often here by default with Breeze
            // 'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class, // Example
        ]);

        // You might also see other middleware configurations here, like:
        // $middleware->web(append: [ ... ]);
        // $middleware->api(prepend: [ ... ]);
        // $middleware->redirectGuestsTo('/login'); // Breeze might add this

    }) // End of withMiddleware closure
    ->withExceptions(function (Exceptions $exceptions) {
        // ... exception handling ...
    })->create();
