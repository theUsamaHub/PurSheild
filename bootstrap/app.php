<?php

use App\Http\Middleware\IpRestrictionMiddleware;
use App\Http\Middleware\MaintenanceModeMiddleware;
use App\Http\Middleware\RedirectAuthenticatedFromPublic;
use App\Middleware\CheckPermission;
use App\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->group(function () {
                require base_path('routes/auth.php');
                require base_path('routes/admin.php');
            });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append([
            MaintenanceModeMiddleware::class,
        ]);

        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => CheckPermission::class,
            'ip-restrict' => IpRestrictionMiddleware::class,
            'guest.public' => RedirectAuthenticatedFromPublic::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
