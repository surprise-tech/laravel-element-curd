<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/18
 */

namespace Wyz\ElementCurd\Show;

use Illuminate\Contracts\Support\Renderable;

class Show implements Renderable
{
    /**
     * 标题.
     */
    protected string $title = '';

    /**
     * 操作实例.
     */
    protected $model;

    /**
     * 所有字段.
     */
    protected array $fields = [];

    /**
     * Descriptions 属性.
     */
    protected array $binds = [];

    /**
     * 初始化回调函数.
     */
    protected \Closure|null $builderCallback = null;

    /**
     * 初始化.
     *
     * @param $model
     */
    public function __construct($model, ?\Closure $builderCallback = null)
    {
        $this->model = $model;
        $this->builderCallback = $builderCallback;
    }

    /**
     * Create a show instance.
     *
     * @param mixed ...$params
     *
     * @return $this
     */
    public static function make(...$params): static
    {
        return new static(...$params);
    }

    /**
     * 增加字段.
     */
    public function field(string $name, string $label): Field
    {
        $field = new Field(data_get($this->model, $name), $label);
        $this->fields[] = $field;

        return $field;
    }

    /**
     * 设置边框.
     *
     * @return $this
     */
    public function border(bool $flag = true): static
    {
        $this->binds['border'] = $flag;

        return $this;
    }

    /**
     * 一行 Descriptions Item 的数量.
     *
     * @return $this
     */
    public function column(int $column = 3): static
    {
        $this->binds['column'] = $column;

        return $this;
    }

    /**
     * 标题.
     *
     * @return $this
     */
    public function title(string $title = ''): static
    {
        $this->binds['title'] = $title;

        return $this;
    }

    /**
     * 渲染json.
     */
    public function render()
    {
        $this->builderCallback && call_user_func($this->builderCallback, $this);
        $fieldOptions = [];
        foreach ($this->fields as $field) {
            $fieldOptions[] = $field->render();
        }

        return [
            'options' => array_merge([
                'title' => $this->title,
                'border' => true,
            ], $this->binds),
            'field' => $fieldOptions,
        ];
    }
}
