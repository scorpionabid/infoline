<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    // app/Exceptions/Handler.php

    // app/Exceptions/Handler.php

public function register(): void
{
    $this->reportable(function (Throwable $e) {
        try {
            $context = [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ];

            // Təhlükəsiz şəkildə auth məlumatlarını əlavə edirik
            if (app()->bound('auth')) {
                $user = auth()->user();
                if ($user) {
                    $context['user_id'] = $user->id;
                }
            }

            // Request məlumatlarını əlavə edirik
            if ($request = request()) {
                $context['request_url'] = $request->fullUrl();
                $context['request_method'] = $request->method();
                $context['ip_address'] = $request->ip();
                $context['user_agent'] = $request->userAgent();
            }

            Log::error('Exception caught: ' . $e->getMessage(), $context);
            
        } catch (\Exception $loggingException) {
            // Logging prosesində yaranan xətaları handle edirik
            Log::error('Logging failed: ' . $loggingException->getMessage());
        }
    });

    // API xətaları
    $this->renderable(function (NotFoundHttpException $e, $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found'
            ], 404);
        }
    });

    // Validation xətaları
    $this->renderable(function (ValidationException $e, $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);
        }
    });

    // Authentication xətaları
    $this->renderable(function (AuthenticationException $e, $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    });
}
}