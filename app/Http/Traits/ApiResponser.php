<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponser
{
    public function success($data, string $message, int $code = 200): JsonResponse
    {
        return response()->json(['data' => $data, 'message' => $message, 'code' => $code], $code, [], JSON_UNESCAPED_UNICODE);
    }

    public function error($data, string $message, int $code): JsonResponse
    {
        return response()->json(['error' => $data, 'message' => $message, 'code' => $code], $code, [], JSON_UNESCAPED_UNICODE);
    }
}
