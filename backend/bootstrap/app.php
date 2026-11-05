<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php', 
        api: __DIR__.'/../routes/api.php', 
        commands: __DIR__.'/../routes/console.php', 
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(__DIR__.'/../routes/api.php');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void { 
        $middleware->statefulApi(); 
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'api/auth/login',
            'api/auth/logout'
        ]);
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
        $middleware->alias([
            'request.id' => \App\Http\Middleware\RequestId::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void { 
        
    })
    ->withProviders([
        \App\Providers\AppServiceProvider::class,
    ])
    ->create();
