<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponser
{
    public function success(array $data, string $message = null, int $code = 200): JsonResponse
    {
        return response()->json(['data' => $data, 'message' => $message, 'code' => $code], $code, [], JSON_UNESCAPED_UNICODE);
    }

    public function error(array $data, string $message = null, int $code): JsonResponse
    {
        return response()->json(['error' => $data, 'message' => $message, 'code' => $code], $code, [], JSON_UNESCAPED_UNICODE);
    }
}
