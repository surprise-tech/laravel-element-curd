<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/20
 */

namespace Wyz\ElementCurd\Form;

class File extends FormItem
{
    protected string $type = 'cu-upload';

    protected array $binds = [
       'options' => [
           'multiple' => false,
           'method' => 'post',
           'name' => 'admin_file',
           'auto-upload' => true,
           'show-file-list' => false,
           'list-type' => 'text',
       ],
    ];

    /**
     * 开启多文件上传.
     */
    public function multiple(bool $flag = true): static
    {
        $this->binds['options']['multiple'] = $flag;
        $this->binds['options']['show-file-list'] = $flag;

        return $this;
    }
}
