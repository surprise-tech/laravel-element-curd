<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/18
 */

namespace Wyz\ElementCurd\Show;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;

class Field implements Renderable
{
    /**
     * 内容.
     */
    protected mixed $content;

    /**
     * 标签.
     */
    protected string $label;

    /**
     * 自定义显示函数.
     */
    protected \Closure $asCallback;

    /**
     * 字段初始化.
     */
    public function __construct(mixed $content, string $label = '')
    {
        $this->content = $content;
        $this->label = $label;
        $this->asCallback = fn ($val) => ($val instanceof Carbon) ? $val->format('Y-m-d H:i:s') : $val;
    }

    /**
     * 自定义显示.
     */
    public function as(\Closure $callable): static
    {
        $this->asCallback = $callable;

        return $this;
    }

    /**
     * 渲染json.
     */
    public function render(): array
    {
        return [
            'bind' => [
                'label' => $this->label,
            ],
            'data' => is_callable($this->asCallback) ? call_user_func($this->asCallback, $this->content) : $this->content,
        ];
    }
}
