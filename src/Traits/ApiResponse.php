<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/18
 */

namespace Wyz\ElementCurd\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * 成功
     */
    public function success(int $httpCode = 200): JsonResponse
    {
        return response()->json([], $httpCode);
    }

    /**
     * 失败.
     */
    public function failed(string $msg = 'failed', int $code = 400, int $httpCode = 400, array $innerError = []): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'message' => $msg,
            'innerError' => $innerError,
        ], $httpCode);
    }

    /**
     * 自定义返回.
     */
    public function sendData(mixed $data = [], int $httpCode = 200): JsonResponse
    {
        return response()->json($data, $httpCode);
    }
}
