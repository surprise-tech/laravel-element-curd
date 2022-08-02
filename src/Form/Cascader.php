<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/8/2
 */

namespace Wyz\ElementCurd\Form;

class Cascader extends FormItem
{
    protected string $type = 'el-cascader';

    protected string $placeholderFormat = '请选择%label';

    protected array $binds = [
        'options' => [],
    ];

    public function options(array $options = [])
    {
        $this->binds['options'] = $options;

        return $this;
    }
}
