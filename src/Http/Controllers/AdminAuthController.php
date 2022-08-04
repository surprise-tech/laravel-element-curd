<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/20
 */

namespace Wyz\ElementCurd\Http\Controllers;

use App\Enums\AdminStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Wyz\ElementCurd\Enums\MenuKeepAlive;
use Wyz\ElementCurd\Enums\MenuShowLink;
use Wyz\ElementCurd\Help;
use Wyz\ElementCurd\Model\Menu;
use Wyz\ElementCurd\Traits\ApiResponse;

class AdminAuthController extends Controller
{
    use ApiResponse;

    /**
     * 登录.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        $admin = app(config('admin.table.model'))->where('username', $request->input('username'))->first();
        if (
            empty($admin) ||
            AdminStatus::Normal !== $admin->status ||
            !password_verify($request->input('password'), $admin->password)
        ) {
            return $this->failed('账号密码错误！');
        }

        // 登录成功
        return $this->respondWithToken($admin);
    }

    /**
     * 退出登录.
     */
    public function logout(): JsonResponse
    {
        auth(config('admin.guard'))->logout();

        return $this->success();
    }

    /**
     * 获取路由.
     */
    public function getAsyncRoutes(): JsonResponse
    {
        $admin = app(config('admin.table.model'))->with('roles')->findOrFail(auth(config('admin.guard'))->id());
        $menus = Menu::query()->orderBy('rank')->whereHas('roles', function ($roles) use ($admin) {
            $roles->whereIn('id', (array) data_get($admin, 'roles.*.id'));
        })->get();

        return $this->sendData(Help::getTreeData($menus, 0, 'pid', 'id', function ($item) {
            $temp = [
                'path' => data_get($item, 'path'),
                'meta' => [
                    'title' => data_get($item, 'title'),
                    'showLink' => MenuShowLink::OPEN === data_get($item, 'show_link'),
                    'keepAlive' => MenuKeepAlive::OPEN === data_get($item, 'keep_alive'),
                ],
            ];
            if ($name = data_get($item, 'name')) {
                $temp['name'] = $name;
            }
            if ($redirect = data_get($item, 'redirect')) {
                $temp['redirect'] = $redirect;
            }
            foreach (['icon', 'frame_src'] as $key) {
                if ($val = data_get($item, $key)) {
                    $temp['meta'][$key] = $val;
                }
            }

            return $temp;
        }));
    }

    /**
     * 登录返回信息.
     */
    protected function respondWithToken($admin): JsonResponse
    {
        $guard = config('admin.guard');

        return $this->sendData([
            'name' => $admin->name,
            'avatar' => $admin->avatar,
            'accessToken' => auth($guard)->login($admin),
            'token_type' => 'Bearer',
            'expires' => auth($guard)->factory()->getTTL() * 60,
        ]);
    }
}
