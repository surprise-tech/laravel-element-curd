<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/21
 */

namespace Wyz\ElementCurd\Form;

class Custom extends FormItem
{
    /**
     * 设置自定义组件名.
     *
     * @return $this
     */
    public function component(string $name): static
    {
        $this->type = $name;

        return $this;
    }

    /**
     * 自定义参数.
     */
    public function options(array $options): static
    {
        $this->binds['options'] = $options;

        return $this;
    }
}
