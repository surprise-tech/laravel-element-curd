<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/18
 */

namespace Wyz\ElementCurd\Grid;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Carbon;

class Column implements Renderable
{
    /**
     * 字段索引.
     */
    protected string $name;

    /**
     * 列名称.
     */
    protected string $label;

    /**
     * 列宽度.
     */
    protected string $width = '';

    /**
     * 自定义数据.
     */
    protected mixed $custom = false;

    /**
     * 转换字段函数.
     */
    protected \Closure $displayCallback;

    public function __construct($name, $label = '')
    {
        $this->name = $name;
        $this->label = $label;
        $this->displayCallback = fn ($val, $row) => ($val instanceof Carbon) ? $val->format('Y-m-d H:i:s') : $val;
    }

    /**
     * 列宽度.
     */
    public function width(string $width = ''): static
    {
        $this->width = $width;

        return $this;
    }

    /**
     * 自定义输出.
     */
    public function display(\Closure $callback): static
    {
        $this->displayCallback = $callback;

        return $this;
    }

    /**
     * 获取自定义输出函数.
     */
    public function getDisplayCallback(): \Closure
    {
        return $this->displayCallback;
    }

    /**
     * label 类型.
     */
    public function label(array $options = []): static
    {
        $this->custom = [
            'type' => 'tag',
            'options' => $options,
        ];

        return $this;
    }

    /**
     * 图标.
     */
    public function icon(): static
    {
        $this->custom = [
            'type' => 'icon',
        ];

        return $this;
    }

    /**
     * 图片.
     */
    public function image(string $width = '44px', string $height = '44px'): static
    {
        $this->custom = [
            'type' => 'image',
            'style' => [
                'width' => $width,
                'height' => $height,
                'margin' => '2px',
            ],
        ];

        return $this;
    }

    /**
     * 设置其他自定义.
     */
    public function custom(array $options = []): static
    {
        $options['__custom__'] = true;
        $this->custom = $options;

        return $this;
    }

    /**
     * 多项label.
     */
    public function multipleLabel(string $color = 'success'): static
    {
        $this->custom = [
            'type' => 'multiple-tag',
            'color' => $color,
        ];

        return $this;
    }

    /**
     * 文本编辑框.
     */
    public function textInput(bool $event = false): static
    {
        $this->custom = [
            'type' => 'column-edit',
            'element-tag' => 'input',
            'event' => $event ? 1 : 0,
        ];

        return $this;
    }

    /**
     * 开关.
     */
    public function switch(string|null $active = null, string|null $inactive = null, bool $event = false): static
    {
        $this->custom = [
            'type' => 'column-edit',
            'element-tag' => 'switch',
            'event' => $event ? 1 : 0,
            'bind' => [],
        ];
        $active && $this->custom['bind']['active-text'] = $active;
        $inactive && $this->custom['bind']['inactive-text'] = $inactive;

        return $this;
    }

    /**
     * 渲染.
     */
    public function render(): array
    {
        $options = [
            'bind' => [
                'label' => $this->label,
                'prop' => $this->name,
            ],
            'custom' => $this->custom,
        ];
        if ($this->width) {
            $options['bind']['width'] = $this->width;
        }

        return $options;
    }
}
