<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/19
 */

return [
    // 默认头像
    'avatar' => env('APP_URL').'/admin_default_avatar.jpg',

    // 路由配置
    'route' => [
        // 路由前缀
        'prefix' => env('ADMIN_ROUTE_PREFIX', 'admin'),

        // 不受权限控制的路由
        'except' => [
            'login',
            'logout',
        ],

        // 请在app/Http/Kernel.php中进行配置
        'middleware' => 'admin',
    ],

    // 数据表配置
    'table' => [
        'model_has_roles' => [
            'name' => 'admin_has_roles',
            'foreign_pivot_key' => 'admin_id',
            'related_pivot_key' => 'role_id',
        ],
        'model' => \App\Models\Admin::class,
        'account' => [
            'name' => '超级管理员',
            'username' => 'admin',
            'password' =>'admin',
        ],
    ],

    // 认证guard, 请在config/auth.php中配置
    'guard' => 'admin',
];
