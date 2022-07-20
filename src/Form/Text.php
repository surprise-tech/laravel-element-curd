<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/18
 */

namespace Wyz\ElementCurd\Form;

class Text extends FormItem
{
    protected string $type = 'el-input';
    protected string $placeholderFormat = '请输入 %label';
    protected string $requiredFormat = '%label 必填';
}
