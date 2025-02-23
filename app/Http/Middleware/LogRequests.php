<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequests
{
    public function handle(Request $request, Closure $next): Response 
    {
        // Request başlanğıc vaxtı
        $startTime = microtime(true);
        
        // Unique request ID
        $requestId = uniqid('req_');
        
        // User ID-ni təyin et
        $userId = $request->user() ? $request->user()->id : 0;
        
        // Request məlumatlarını logla
        Log::info('Request Started', [
            'request_id' => $requestId,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'user_id' => $userId,
            'params' => $this->filterSensitiveData($request->all())
        ]);

        // Request-i handle et
        $response = $next($request);

        // Response vaxtını hesabla
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        // Response məlumatlarını logla 
        Log::info('Request Completed', [
            'request_id' => $requestId,
            'duration_ms' => $duration,
            'status' => $response->getStatusCode(),
            'memory_usage' => $this->formatBytes(memory_get_peak_usage()),
            'user_id' => $userId
        ]);

        return $response;
    }

    private function filterSensitiveData(array $data): array
    {
        $sensitiveFields = [
            'password',
            'token',
            'api_key',
            'secret',
            'card',
            'cvv'
        ];

        return array_map(function ($value) use ($sensitiveFields) {
            if (is_array($value)) {
                return $this->filterSensitiveData($value);
            }

            // Həssas məlumatları maskla
            foreach ($sensitiveFields as $field) {
                if (stripos($value, $field) !== false) {
                    return '********';
                }
            }

            return $value;
        }, $data);
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        return round($bytes / pow(1024, $pow), 2) . ' ' . $units[$pow];
    }
}