<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/19
 */

namespace Wyz\ElementCurd\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $table = 'roles';

    protected $guarded = [];

    /*
     * 对应的权限.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id',
            'permission_id');
    }

    /*
     * 对应的菜单.
     */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'role_has_menus',
            'role_id', 'menu_id');
    }
}
