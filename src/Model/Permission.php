<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/19
 */

namespace Wyz\ElementCurd\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Wyz\ElementCurd\Traits\ModelTree;

class Permission extends Model
{
    use ModelTree;

    protected $table = 'permissions';

    protected $guarded = [];

    protected $casts = [
        'http_method' => 'json',
    ];

    public function __construct(array $attributes = [])
    {
        $this->titleColumn = 'title';
        $this->parentColumn = 'pid';
        $this->orderColumn = 'id';
        parent::__construct($attributes);
    }

    /*
     * 对应的角色.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions', 'permission_id',
            'role_id');
    }
}
