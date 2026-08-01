<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\UserRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Core Unified API Response Macro
        Response::macro('api', function (bool $success, string $message, mixed $data = null, int $status = 200) {
            $response = ['success' => $success, 'message' => $message];
            if ($data !== null) {
                $response['data'] = $data;
            }
            return Response::json($response, $status);
        });

        // One-Line Standard Response Macros
        Response::macro('success', fn (mixed $data = null, string $message = 'Success', int $status = 200) => Response::api(true, $message, $data, $status));
        Response::macro('error', fn (string $message = 'Error', int $status = 400, mixed $data = null) => Response::api(false, $message, $data, $status));
        Response::macro('unauthorized', fn (string $message = 'Unauthenticated', mixed $data = null) => Response::api(false, $message, $data, 401));
        Response::macro('forbidden', fn (string $message = 'Forbidden', mixed $data = null) => Response::api(false, $message, $data, 403));
        Response::macro('notFound', fn (string $message = 'Resource not found', mixed $data = null) => Response::api(false, $message, $data, 404));
        Response::macro('serverError', fn (string $message = 'Internal server error', mixed $data = null) => Response::api(false, $message, $data, 500));
    }
}
