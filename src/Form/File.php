<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/20
 */

namespace Wyz\ElementCurd\Form;

use Wyz\ElementCurd\Help;

class File extends FormItem
{
    protected string $type = 'cu-upload';

    protected array $binds = [
       'options' => [
           'multiple' => false,
           'method' => 'post',
           'name' => 'admin_file',
           'auto-upload' => true,
           'show-file-list' => false
       ]
    ];

    /**
     * 开启多图上传.
     */
    public function multiple(bool $flag = true) : static
    {
        $this->binds['options']['multiple'] = $flag;
        return $this;
    }
}