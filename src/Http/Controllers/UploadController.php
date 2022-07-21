<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/20
 */

namespace Wyz\ElementCurd\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Wyz\ElementCurd\Traits\ApiResponse;

class UploadController extends Controller
{
    use ApiResponse;

    /**
     * 文件上传.
     */
    public function upload(Request $request): JsonResponse
    {
        if ($request->hasFile('file')) {
            $upload = config('admin.upload');
            $path = Storage::disk($upload['disk'])->put($upload['dir'], $request->file('file'));

            return $this->sendData([
                'path' => $path,
                'url' => Storage::disk($upload['disk'])->url($path),
            ]);
        }

        return $this->failed('请上传文件！');
    }
}
