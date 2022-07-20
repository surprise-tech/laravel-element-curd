<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/18
 */

namespace Wyz\ElementCurd\Form;

class SwitchField extends FormItem
{
    protected string $type = 'el-switch';

    protected string|null $placeholder = null;

    protected array $switchOptions = [
        'off' => 'off',
        'on' => 'on',
    ];

    /**
     * 设置开关的值.
     */
    public function options(array $switchOptions = []): static
    {
        if (isset($switchOptions['off'])) {
            $this->switchOptions['off'] = $switchOptions['off'];
        }
        if (isset($switchOptions['on'])) {
            $this->switchOptions['on'] = $switchOptions['on'];
        }

        return $this;
    }

    /**
     * 设置默认值.
     */
    public function default(mixed $value): static
    {
        $this->defaultVal = $value === $this->switchOptions['on'];

        return $this;
    }
}
