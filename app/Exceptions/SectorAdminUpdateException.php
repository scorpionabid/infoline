<?php

namespace App\Exceptions;

use Exception;

class SectorAdminUpdateException extends Exception
{
    public function __construct(string $message = "Sektor admin təyinatı zamanı xəta baş verdi", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage()
            ], 500);
        }

        return back()->with('error', $this->getMessage());
    }
}
