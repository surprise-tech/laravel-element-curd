<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/20
 */

namespace Wyz\ElementCurd;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Wyz\ElementCurd\Http\Controllers\AdminAuthController;
use Wyz\ElementCurd\Http\Controllers\MenuController;
use Wyz\ElementCurd\Http\Controllers\PermissionController;
use Wyz\ElementCurd\Http\Controllers\RoleController;
use Wyz\ElementCurd\Http\Middleware\Authenticate;

class AdminServiceProvider extends ServiceProvider
{
    public function register()
    {
        $middleware = config('admin.route.middleware');
        // 注册中间件
        $this->app['router']->aliasMiddleware($middleware, Authenticate::class);

        // 注册路由
        Route::group([
            'prefix' => config('admin.route.prefix'),
            'middleware' => $middleware,
        ], function (Router $router) {
            $router->post('login', [AdminAuthController::class, 'login']);
            $router->get('logout', [AdminAuthController::class, 'logout']);
            $router->get('getAsyncRoutes', [AdminAuthController::class, 'getAsyncRoutes']);
            $router->resource('/auth/permissions', PermissionController::class);
            $router->resource('/auth/roles', RoleController::class);
            $router->resource('/auth/users', config('admin.auth_controller'));
            $router->resource('/auth/menus', MenuController::class);
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config' => config_path('/'),
            __DIR__.'/../database/migrations' => database_path('migrations'),
            __DIR__.'/../assets/admin_default_avatar.jpg' => public_path('admin_default_avatar.jpg'),
        ]);
    }
}
