<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $isInternalApi = fn (Request $request): bool => $request->is('api/*');

        $errorResponse = static function (
            string $code,
            string $message,
            int $status,
            array $details = []
        ) {
            return response()->json([
                'success' => false,
                'data' => null,
                'error' => array_filter([
                    'code' => $code,
                    'message' => $message,
                    'details' => $details ?: null,
                ]),
            ], $status);
        };

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, \Throwable $exception) => $isInternalApi($request)
        );

        $exceptions->render(function (AuthenticationException $exception, Request $request) use ($isInternalApi, $errorResponse) {
            return $isInternalApi($request)
                ? $errorResponse('unauthenticated', 'AutenticaÃ§Ã£o necessÃ¡ria.', 401)
                : null;
        });

        $exceptions->render(function (AuthorizationException $exception, Request $request) use ($isInternalApi, $errorResponse) {
            return $isInternalApi($request)
                ? $errorResponse('forbidden', 'Acesso nÃ£o autorizado.', 403)
                : null;
        });

        $exceptions->render(function (ValidationException $exception, Request $request) use ($isInternalApi, $errorResponse) {
            return $isInternalApi($request)
                ? $errorResponse('validation_failed', 'Os dados informados sÃ£o invÃ¡lidos.', 422, $exception->errors())
                : null;
        });

        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) use ($isInternalApi, $errorResponse) {
            if (! $isInternalApi($request)) {
                return null;
            }

            $status = $exception->getStatusCode();

            return $errorResponse(
                $status === 403 ? 'forbidden' : 'http_error',
                $status === 403 ? 'Acesso nÃ£o autorizado.' : 'NÃ£o foi possÃ­vel concluir a requisiÃ§Ã£o.',
                $status
            );
        });

        $exceptions->render(function (\Throwable $exception, Request $request) use ($isInternalApi, $errorResponse) {
            return $isInternalApi($request)
                ? $errorResponse('internal_error', 'Ocorreu um erro interno.', 500)
                : null;
        });
    })->create();
