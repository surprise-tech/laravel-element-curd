<?php

/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/19
 */

namespace Wyz\ElementCurd\Traits;

use Wyz\ElementCurd\Help;

trait ModelTree
{
    /**
     * 父级ID字段名称.
     */
    protected string $parentColumn = 'pid';

    /**
     * 排序字段名称.
     */
    protected string $orderColumn = 'sort';

    /**
     * 标题字段名称.
     */
    protected string $titleColumn = 'name';

    /**
     * 生成treeSelect项.
     */
    public static function options(string $rootText = null, \Closure|null $callback = null): array
    {
        $model = new static();
        if ($callback instanceof \Closure) {
            $data = self::query()->where($callback)->orderBy($model->orderColumn)->get();
        } else {
            $data = self::query()->orderBy($model->orderColumn)->get();
        }
        $titleColumn = $model->titleColumn;
        $valueColumn = $model->getKeyName();

        return array_merge($rootText ? [[
            'label' => $rootText,
            'value' => 0,
        ]] : [], Help::getTreeData($data, 0, $model->parentColumn, $valueColumn, function ($item) use ($titleColumn, $valueColumn) {
            return [
                'label' => data_get($item, $titleColumn),
                'value' => data_get($item, $valueColumn),
            ];
        }));
    }
}
