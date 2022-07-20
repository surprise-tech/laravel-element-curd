<?php

/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/19
 */

namespace Wyz\ElementCurd\Enums;

enum MenuKeepAlive: int
{
    case OPEN = 1; // 开启
    case CLOSE = 0; // 关闭
}
