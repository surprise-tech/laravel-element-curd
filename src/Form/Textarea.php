<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/19
 */

namespace Wyz\ElementCurd\Form;

class Textarea extends Text
{
    protected array $binds = [
        'type' => 'textarea',
        'autocomplete' => 'off',
        'autosize' => [
            'minRows' => 4,
            'maxRows' => 6,
        ],
    ];
}
