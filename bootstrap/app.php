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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // Sudah login lalu membuka /login atau /admin/login: staff ke dashboard, pelanggan ke beranda
    $middleware->redirectUsersTo(fn ($request) => in_array($request->user()?->role, ['admin', 'owner'], true)
    ? route('admin.dashboard')
    : route('home'));

    // Staff yang masih login tidak bisa membuka halaman pelanggan
    $middleware->appendToGroup('web', \App\Http\Middleware\StaffKeDashboard::class);

        $middleware->validateCsrfTokens(except: ['midtrans/notification']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();