<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoggingController extends Controller
{
    public function logClientError(Request $request)
    {
        $errorData = $request->validate([
            'message' => 'required|string',
            'stack' => 'nullable|string',
            'url' => 'nullable|url',
            'user_id' => 'nullable|integer'
        ]);

        Log::channel('client_error_log')->error('Client-side JavaScript Error', $errorData);

        return response()->json(['status' => 'logged']);
    }
}