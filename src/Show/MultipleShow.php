<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/8/4
 */

namespace Wyz\ElementCurd\Show;

use Illuminate\Contracts\Support\Renderable;

class MultipleShow implements Renderable
{
    protected array $descriptions = [];

    public static function make(): static
    {
        return new static();
    }

    /**
     * 追加普通描述组件.
     */
    public function addDescription(Show $show): static
    {
        $this->descriptions[] = [
            'custom' => false,
            'type' => 'descriptions',
            'data' => $show->render(),
        ];

        return $this;
    }

    /**
     * 追加表格.
     */
    public function addTable(Table $table): static
    {
        $this->descriptions[] = [
            'custom' => false,
            'type' => 'table',
            'data' => $table->render(),
        ];

        return $this;
    }

    /**
     * 追加自定义组件.
     */
    public function addCustom(string $component, array $data = [])
    {
        $this->descriptions[] = [
            'custom' => true,
            'type' => $component,
            'data' => $data,
        ];
    }

    /**
     * 渲染.
     */
    public function render()
    {
        return $this->descriptions;
    }
}
