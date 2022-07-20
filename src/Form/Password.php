<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/18
 */

namespace Wyz\ElementCurd\Form;

class Password extends Text
{
    protected mixed $defaultVal = '';

    public function default($value): static
    {
        $this->defaultVal = '';

        return $this;
    }

    protected array $binds = [
        'type' => 'password',
        'autocomplete' => 'off',
    ];
}
