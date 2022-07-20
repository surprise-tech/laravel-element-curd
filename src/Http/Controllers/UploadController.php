<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/20
 */

namespace Wyz\ElementCurd\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Wyz\ElementCurd\Traits\ApiResponse;

class UploadController extends Controller
{
    use ApiResponse;

    /**
     * 文件上传.
     */
    public function upload(Request $request)
    {
        return $this->sendData([
            'path' => '/admin/terss.png',
            'url' => 'http://demo.test/a.png'
        ]);
    }
}