<?php

namespace Wyz\ElementCurd\Form;

class TreeSelect extends FormItem
{
    protected string $type = 'el-tree-select';

    /**
     * 默认参数.
     */
    protected array $binds = [
        'default-expand-all' => true,
        'empty-text' => '暂无数据',
        'clearable' => true,
        'check-strictly' => true,
        'style' => [
            'width' => '100%',
        ],
    ];

    /**
     * 设置strictly.
     */
    public function strictly(bool $flag): static
    {
        $this->binds['check-strictly'] = $flag;

        return $this;
    }

    /**
     * 设置选项.
     *
     * @param $options
     *
     * @return $this
     */
    public function options($options): static
    {
        $this->binds['data'] = $options;

        return $this;
    }
}
