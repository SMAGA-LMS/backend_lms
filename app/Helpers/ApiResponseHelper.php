<?php

namespace App\Helpers;

class ApiResponseHelper
{
    public function successResponse(string $message, $data, int $codeResponse)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ], $codeResponse);
    }

    public function errorResponse(string $message, $errors, int $codeResponse)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $this->formatErrors($errors),
        ], $codeResponse);
    }

    private function formatErrors($errors)
    {
        // Jika errors sudah dalam format objek, kembalikan apa adanya
        if (is_array($errors) && array_keys($errors) !== range(0, count($errors) - 1)) {
            return $errors;
        }

        // Jika errors adalah array numerik, bungkus dalam objek dengan key 'general'
        return [
            'general' => (array) $errors
        ];
    }
}
