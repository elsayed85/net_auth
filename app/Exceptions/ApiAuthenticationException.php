<?php

namespace App\Exceptions;

use Exception;

class ApiAuthenticationException extends Exception
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report(): void
    {
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "success" => false,
            "auth" => false,
            "message" => "Unauthenticated"
        ], 401);
    }
}
