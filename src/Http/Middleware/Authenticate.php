<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/19
 */

namespace Wyz\ElementCurd\Http\Middleware;

use Wyz\ElementCurd\Help;
use Wyz\ElementCurd\Traits\ApiResponse;

class Authenticate
{
    use ApiResponse;

    public function handle($request, \Closure $next)
    {
        $admin = auth(config('admin.guard'))->user();
        if ($admin) {
            if (!($this->shouldPassThrough() || $this->checkPermission($admin))) {
                return $this->failed('权限不足！', 403, 403);
            }
        } else {
            if (!$this->shouldPassThrough()) {
                return $this->failed('认证失败！', 401, 401);
            }
        }

        return $next($request);
    }

    /**
     * 验证请求的URL是否有权限(排除在外的).
     */
    protected function shouldPassThrough(): bool
    {
        // 排除在外的
        $excepts = (array) config('admin.route.except', []);

        foreach ($excepts as $except) {
            $except = Help::adminBasePath($except);

            if ('/' !== $except) {
                $except = trim($except, '/');
            }

            if (Help::matchRequestPath($except)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 验证请求的URL是否有权限(角色路由权限).
     */
    protected function checkPermission($admin): bool
    {
        return $admin && $admin->allPermissions()->first(function ($permission) {
            $http_method = implode(',', data_get($permission, 'http_method', []));
            $method = $http_method ? $http_method.':' : '';
            $http_path = explode("\n", data_get($permission, 'http_path'));
            foreach ($http_path as $path) {
                $path = Help::adminBasePath($path);

                if ('/' !== $path) {
                    $path = trim($path, '/');
                }

                if (Help::matchRequestPath($method.$path)) {
                    return true;
                }
            }

            return false;
        });
    }
}
