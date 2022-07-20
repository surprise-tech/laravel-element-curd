<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/18
 */

namespace Wyz\ElementCurd\Enums;

enum FormMode: string
{
    case MODE_EDIT = 'edit'; // 编辑
    case MODE_CREATE = 'create'; // 创建
    case MODE_DELETE = 'delete'; // 删除
}
