<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/8/4
 */

namespace Wyz\ElementCurd\Show;

use Illuminate\Contracts\Support\Renderable;
use Wyz\ElementCurd\Grid\Column;

class Table implements Renderable
{
    /**
     * 表格标题.
     */
    protected string $tableTitle = '';

    /**
     * 表格数据.
     */
    protected mixed $data = [];

    /**
     * 要显示的字段.
     */
    protected array $column = [];

    /**
     * 初始化回调函数.
     */
    protected \Closure|null $builderCallback = null;

    /**
     * 初始化.
     *
     * @param $model
     */
    public function __construct(mixed $data = [], ?\Closure $builderCallback = null)
    {
        $this->data = $data;
        $this->builderCallback = $builderCallback;
    }

    /**
     * Create a table instance.
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
     * 设置标题.
     *
     * @return $this
     */
    public function title(string $title = ''): static
    {
        $this->tableTitle = $title;

        return $this;
    }

    /**
     * 设置列字段.
     */
    public function column($field, $label): Column
    {
        $column = new Column($field, $label);
        $this->column[$field] = $column;

        return $column;
    }

    /**
     * 渲染.
     */
    public function render()
    {
        $this->builderCallback && call_user_func($this->builderCallback, $this);
        $column = [];
        foreach ($this->column as $col) {
            $column[] = $col->render();
        }
        $data = [];
        foreach ($this->data as $item) {
            $temp = [];
            foreach ($this->column as $field => $col) {
                $temp[$field] = call_user_func($col->getDisplayCallback(), data_get($item, $field), $item);
            }
            $data[] = $temp;
        }

        return [
            'title' => $this->tableTitle,
            'column' => $column,
            'data' => $data,
            'bind' => [
                'border' => true,
                'size' => 'default',
            ],
        ];
    }
}
