<?php

namespace App\Helpers;

class ApiResponseHelper
{
    public function successResponse(string $message, $data, int $codeResponse)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $codeResponse);
    }

    public function errorResponse(string $message, $errors, int $codeResponse)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $codeResponse);
    }
}
