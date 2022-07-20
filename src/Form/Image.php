<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/20
 */

namespace Wyz\ElementCurd\Form;


use Wyz\ElementCurd\Help;

class Image extends File
{
    protected string $type = 'cu-upload';

    public function __construct(string $column, string $label = '')
    {
        parent::__construct($column, $label);
        $this->binds['options']['accept'] = 'image/*';
    }
}