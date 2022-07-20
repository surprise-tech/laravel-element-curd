<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/19
 */

namespace Wyz\ElementCurd\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Wyz\ElementCurd\Enums\MenuKeepAlive;
use Wyz\ElementCurd\Enums\MenuShowLink;
use Wyz\ElementCurd\Traits\ModelTree;

class Menu extends Model
{
    use ModelTree;
    protected $table = 'menus';
    protected $guarded = [];
    protected $casts = [
        'show_link' => MenuShowLink::class,
        'keep_alive' => MenuKeepAlive::class,
    ];

    public function __construct(array $attributes = [])
    {
        $this->titleColumn = 'title';
        $this->parentColumn = 'pid';
        $this->orderColumn = 'id';
        parent::__construct($attributes);
    }

    /**
     * 对应的角色.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_menus',
            'menu_id', 'role_id');
    }
}
