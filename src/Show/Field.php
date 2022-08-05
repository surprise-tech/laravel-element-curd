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
     * 类型.
     */
    protected string $type = 'text';

    /**
     * 附加属性.
     */
    protected array $options = [];

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
     * label类型.
     */
    public function label(array $options): static
    {
        $this->type = 'tag';
        $this->options = $options;

        return $this;
    }

    /**
     * multiple label类型.
     */
    public function multipleLabel(string $color = 'success', string $effect = 'light'): static
    {
        $this->type = 'multiple-tag';
        $this->options = [
            'color' => $color,
            'effect' => $effect,
        ];

        return $this;
    }

    /**
     * 图片类型.
     */
    public function image(string $width = '44px', string $height = '44px'): static
    {
        $this->type = 'image';
        $this->options = [
            'style' => [
                'width' => $width,
                'heigth' => $height,
            ],
        ];

        return $this;
    }

    /**
     * 自定义组件.
     */
    public function custom(string $component, mixed $options = []): static
    {
        $this->type = $component;
        $this->options = $options;

        return $this;
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
            'type' => $this->type,
            'options' => $this->options,
            'data' => is_callable($this->asCallback) ? call_user_func($this->asCallback, $this->content) : $this->content,
        ];
    }
}
