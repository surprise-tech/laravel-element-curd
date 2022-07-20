<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/18
 */

namespace Wyz\ElementCurd\Form;

class MultipleSelect extends Select
{
    protected array $binds = [
        'options' => [
            'multiple' => true,
            'clearable' => true,
            'style' => [
                'width' => '100%',
            ],
        ]
    ];
}
