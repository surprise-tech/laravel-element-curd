<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/19
 */

namespace Wyz\ElementCurd\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Wyz\ElementCurd\Model\Role;

trait HasRoles
{
    /**
     * 用户所有权限.
     */
    protected Collection|null $allPermissions = null;

    // 对应的角色
    public function roles(): BelongsToMany
    {
        $model_has_roles = config('admin.table.model_has_roles');

        return $this->belongsToMany(Role::class, $model_has_roles['name'],
            $model_has_roles['foreign_pivot_key'], $model_has_roles['related_pivot_key']);
    }

    /**
     * 获取所有权限.
     */
    public function allPermissions(): Collection
    {
        if ($this->allPermissions) {
            return $this->allPermissions;
        }

        return $this->allPermissions = $this->roles
            ->pluck('permissions')
            ->flatten()
            ->keyBy($this->getKeyName());
    }
}
