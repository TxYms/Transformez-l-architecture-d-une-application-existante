<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

final class ApiResponse
{
    public static function success(mixed $data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        return self::make('success', $message, $data, $status);
    }

    public static function error(string $message, int $status, mixed $data = null, array $headers = []): JsonResponse
    {
        return self::make('error', $message, $data, $status, $headers);
    }

    private static function make(string $status, string $message, mixed $data, int $code, array $headers = []): JsonResponse
    {
        return new JsonResponse([
            'status' => $status,
            'message' => $message,
            'data' => self::normalize($data),
        ], $code, $headers, JSON_UNESCAPED_UNICODE);
    }

    private static function normalize(mixed $data): mixed
    {
        if ($data instanceof JsonResource) {
            return $data->response()->getData(true)['data'] ?? null;
        }

        return $data;
    }
}
