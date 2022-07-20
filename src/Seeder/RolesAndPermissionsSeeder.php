<?php

namespace Wyz\ElementCurd\Seeder;

use Illuminate\Database\Seeder;
use Wyz\ElementCurd\Enums\MenuKeepAlive;
use Wyz\ElementCurd\Enums\MenuShowLink;
use Wyz\ElementCurd\Model\Menu;
use Wyz\ElementCurd\Model\Permission;
use Wyz\ElementCurd\Model\Role;

/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/20
 */
class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        if (Role::query()->count()) {
            exit('[error]请勿重复初始化');
        }
        // 创建角色
        $role = Role::query()->create(['name' => 'admin', 'title' => '超级管理员', 'pid' => 0]);
        // 创建权限
        $permissionNames = [
            ['name' => 'super', 'title' => '所有权限', 'http_path' => '*'],
            ['name' => 'home', 'title' => '首页', 'http_path' => '/home*'],
            ['name' => 'auth', 'title' => '超级管理'],
            ['name' => 'auth.users', 'title' => '管理员管理', 'http_path' => '/auth/users*', 'pid' => 3],
            ['name' => 'auth.menus', 'title' => '菜单管理', 'http_path' => '/auth/menus*', 'pid' => 3],
            ['name' => 'auth.roles', 'title' => '角色管理', 'http_path' => '/auth/roles*', 'pid' => 3],
            ['name' => 'auth.permissions', 'title' => '权限管理', 'http_path' => '/auth/permissions*', 'pid' => 3],
        ];
        foreach ($permissionNames as $key => $item) {
            $permission = Permission::query()->create($item);
            if (0 === $key) {
                $role->permissions()->attach($permission->id);
            }
        }

        // 创建菜单
        $menus = [
            [
                'title' => '超级管理',
                'path' => '/auth',
                'redirect' => '/auth/users',
                'icon' => 'lollipop',
            ],
            [
                'path' => '/auth/users',
                'name' => 'AuthUsers',
                'title' => '管理员',
                'pid' => 1,
            ],
            [
                'title' => '角色管理',
                'path' => '/auth/roles',
                'name' => 'AuthRoles',
                'pid' => 1,
            ],
            [
                'path' => '/auth/permission',
                'name' => 'AuthPermissions',
                'title' => '权限管理',
                'pid' => 1,
            ],
            [
                'title' => '菜单管理',
                'path' => '/auth/menus',
                'name' => 'AuthMenus',
                'pid' => 1,
            ],
        ];

        foreach ($menus as $key => $item) {
            $item['show_link'] = MenuShowLink::OPEN;
            $item['keep_alive'] = MenuKeepAlive::OPEN;
            $item['rank'] = ($key + 1);
            $menu = Menu::query()->create($item);
            $role->menus()->attach($menu->id);
        }

        $account = config('admin.table.account');
        $account['password'] = bcrypt($account['password']);
        $admin = app(config('admin.table.model'))->create($account);
        $admin->roles()->attach($role->id);
    }
}
