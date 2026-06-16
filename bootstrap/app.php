<?php
// bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        // Register your custom commands here
        \App\Console\Commands\ImportQuran::class,
        \App\Console\Commands\ImportTranslations::class,
        \App\Console\Commands\ImportTafsir::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {

        // Register route aliases
        $middleware->alias([
            'plan'  => \App\Http\Middleware\PlanMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
