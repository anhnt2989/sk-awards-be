<?php

use App\Exceptions\FileUploadFailedException;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetTeamUrlDefaults;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn ($request) => $request->is('api/*')
        );

        $exceptions->render(function (Throwable $e) {
            if ($e instanceof FileUploadFailedException) {
                return response()->json([
                    'message' => 'Không thể upload file. Vui lòng thử lại.',
                ], 422);
            } else if ($e instanceof ValidationException) {
                return response()->json([
                    'message' => $e->errors(),
                ], 422);
            }
        });
    })->create();
