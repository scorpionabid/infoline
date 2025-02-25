<?php
namespace App\Services\LoggingService;

use Illuminate\Support\Facades\Log;

class ClientErrorLogger
{
    public function logClientError(array $errorData)
    {
        Log::channel('client_error_log')->error('Client Error', $errorData);
    }
}