<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/18
 */

namespace Wyz\ElementCurd\Grid;

use Illuminate\Contracts\Support\Renderable;
use Wyz\ElementCurd\ElementAttributes;

class Grid extends ElementAttributes implements Renderable
{
    /**
     * 表格配置.
     */
    protected array $tableOptions = [
        'title' => '列表',
        'showCreateBtn' => true, // 显示创建按钮
        'showDetailBtn' => true, // 显示详情按钮
        'showEditBtn' => true, // 显示编辑按钮
        'showDeleteBtn' => true, // 显示删除按钮
        'showActions' => true, // 显示操作列.
        'actionWidth' => '150px',
        'showPagination' => true, // 显示分页
        'treeTableOptions' => [
            'enable' => false,
            'pidField' => 'id',
            'rootPid' => 0,
        ],
    ];

    /**
     * 详情配置.
     */
    protected array $detailOptions = [
        '__type__' => 'drawer',
        '__custom__' => false,
        'width' => '50%',
        'title' => '查看详情',
    ];

    /**
     * 编辑配置.
     */
    protected array $editOptions = [
        '__type__' => 'drawer',
        '__custom__' => false,
        'width' => '50%',
        'title' => '编辑',
    ];

    /**
     * 创建配置.
     */
    protected array $createOptions = [
        '__type__' => 'drawer',
        '__cache__' => true,
        '__custom__' => false,
        'width' => '50%',
        'title' => '新增',
    ];

    /**
     * 表格搜索实例.
     */
    protected Filter $filter;

    /**
     * 要显示的字段.
     */
    protected array $column = [];

    /**
     * 模型.
     */
    protected mixed $model;

    /**
     * 主键.
     */
    protected string $keyName = 'id';

    /**
     * 初始化回调函数.
     */
    protected \Closure|null $builderCallback = null;

    /**
     * 其他绑定数据.
     */
    protected array $binds = [
        'border' => true,
        'size' => 'default',
    ];

    /**
     * 初始化.
     *
     * @param null $model
     */
    public function __construct($model = null, ?\Closure $builderCallback = null, string $keyName = 'id')
    {
        $this->filter = new Filter();
        $this->model = is_string($model) ? app($model) : $model;
        $this->keyName = $keyName; // 主键
        $this->builderCallback = $builderCallback;
    }

    /**
     * Create a grid instance.
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
     * 禁用分页.
     */
    public function disablePagination(bool $flag = false): static
    {
        $this->tableOptions['showPagination'] = $flag;

        return $this;
    }

    /**
     * 禁用新增按钮.
     */
    public function disableCreateBtn(bool $flag = false): static
    {
        $this->tableOptions['showCreateBtn'] = $flag;

        return $this;
    }

    /**
     * 禁用详情按钮.
     */
    public function disableShowBtn(bool $flag = false): static
    {
        $this->tableOptions['showDetailBtn'] = $flag;

        return $this;
    }

    /**
     * 禁用actions.
     */
    public function disableActions(bool $flag = false): static
    {
        $this->tableOptions['showActions'] = $flag;

        return $this;
    }

    /**
     * 禁用编辑按钮.
     */
    public function disableEditBtn(bool $flag = false): static
    {
        $this->tableOptions['showEditBtn'] = $flag;

        return $this;
    }

    /**
     * 禁用删除按钮.
     */
    public function disableDeleteBtn(bool $flag = false): static
    {
        $this->tableOptions['showDeleteBtn'] = $flag;

        return $this;
    }

    /**
     * 开启树状表格.
     */
    public function enableTree(bool $enable = true, string $pidField = 'pid', string $keyField = 'id', $root = 0): static
    {
        $this->tableOptions['treeTableOptions'] = [
            'enable' => $enable,
            'keyField' => $keyField,
            'pidField' => $pidField,
            'rootPid' => $root,
        ];

        $this->binds['row-key'] = $keyField;
        $this->binds['default-expand-all'] = true;

        return $this;
    }

    /**
     * 设置搜索字段.
     */
    public function filter(\Closure $callback = null): Filter|static
    {
        if (null === $callback) {
            return $this->filter;
        }
        call_user_func($callback, $this->filter);

        return $this;
    }

    public function tableTitle(string $title = ''): static
    {
        $this->tableOptions['title'] = $title;

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
     * 获取model.
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * 设置详情宽度.
     *
     * @return $this
     */
    public function detailSize(string $width): static
    {
        $this->detailOptions['width'] = $width;

        return $this;
    }

    /**
     * 开启详情弹框.
     */
    public function openDetailDialog(bool $flag = true): static
    {
        $this->detailOptions['__type__'] = $flag ? 'dialog' : 'drawer';

        return $this;
    }

    /**
     * 设置编辑宽度.
     *
     * @return $this
     */
    public function editSize(string $width): static
    {
        $this->editOptions['width'] = $width;

        return $this;
    }

    /**
     * 开启编辑弹框.
     */
    public function openEditDialog(bool $flag = true): static
    {
        $this->editOptions['__type__'] = $flag ? 'dialog' : 'drawer';

        return $this;
    }

    /**
     * 设置创建宽度.
     *
     * @return $this
     */
    public function createSize(string $width): static
    {
        $this->createOptions['width'] = $width;

        return $this;
    }

    /**
     * 开启创建弹框.
     */
    public function openCreateDialog(bool $flag = true): static
    {
        $this->createOptions['__type__'] = $flag ? 'dialog' : 'drawer';

        return $this;
    }

    /**
     * 关闭创建缓存.
     */
    public function setCreateCache(bool $cache = true): static
    {
        $this->createOptions['__cache__'] = $cache;

        return $this;
    }

    /**
     * 创建表单自定义.
     */
    public function customCreate(bool $flag = true): static
    {
        $this->createOptions['__custom__'] = $flag;

        return $this;
    }

    /**
     * 编辑表单自定义.
     */
    public function customEdit(bool $flag = true): static
    {
        $this->editOptions['__custom__'] = $flag;

        return $this;
    }

    /**
     * 详情自定义.
     */
    public function customShow(bool $flag = true): static
    {
        $this->detailOptions['__custom__'] = $flag;

        return $this;
    }

    /**
     * 设置actions的宽度.
     */
    public function actionWidth(string $width = '150px'): static
    {
        $this->tableOptions['actionWidth'] = $width;

        return $this;
    }

    /**
     * 设置弹框大小.
     */
    public function setSize(string $editWidth, string|null $createWidth = null, string|null $detailWidth = null): static
    {
        return $this->editSize($editWidth)
            ->createSize($createWidth ?: $editWidth)
            ->detailSize($detailWidth ?: $editWidth);
    }

    /**
     * 开启弹框.
     */
    public function openDialog(bool $editFlag = true, bool $createFlag = true, bool $detailFlag = true): Grid
    {
        return $this->openEditDialog($editFlag)
            ->openCreateDialog($createFlag)
            ->openDetailDialog($detailFlag);
    }

    /**
     * 渲染数据.
     */
    public function resource(): array
    {
        $this->builderCallback && call_user_func($this->builderCallback, $this);
        $request = request();

        if ($this->tableOptions['showPagination']) { // 存在分页
            $per_page = min($request->input('per_page'), 100);
            $current_page = $request->input('current_page', 1);
            $total = $this->model()->count();
            $resource = $this->search($this->filter->searchRule)->skip(($current_page - 1) * $per_page)->take($per_page)->get();
        } else {
            $resource = $this->search($this->filter->searchRule)->get();
            $total = count($resource);
            $per_page = $total;
            $current_page = 1;
        }

        if ($this->tableOptions['treeTableOptions']['enable']) {
            // 开启树形表格的情况下要加入pidField和keyField字段
            if (!isset($this->column[$this->tableOptions['treeTableOptions']['pidField']])) {
                $this->column[$this->tableOptions['treeTableOptions']['pidField']] = new Column($this->tableOptions['treeTableOptions']['pidField']);
            }

            if (!isset($this->column[$this->tableOptions['treeTableOptions']['keyField']])) {
                $this->column[$this->tableOptions['treeTableOptions']['keyField']] = new Column($this->tableOptions['treeTableOptions']['keyField']);
            }
        }

        $data = [];
        foreach ($resource as $item) {
            $temp = [];
            foreach ($this->column as $field => $column) {
                $temp[$field] = call_user_func($column->getDisplayCallback(), data_get($item, $field), $item);
            }

            // 强制加入主键
            if (!isset($temp[$this->keyName])) {
                $temp[$this->keyName] = data_get($item, $this->keyName);
            }
            $data[] = $temp;
        }

        return [
            'data' => $data,
            'meta' => [
                'total' => $total,
                'current_page' => (int) $current_page,
                'last_page' => $per_page ? ceil($total / $per_page) : 1,
            ],
        ];
    }

    /**
     * 快捷查询.
     */
    protected function search(array $search = [])
    {
        return $this->model()->where(function ($query) use ($search) {
            $ipt = request();
            foreach ($search as $key => $val) {
                // 获取输入，空值跳过
                $search_input = $ipt->input($key);
                if (blank($search_input)) {
                    continue;
                }

                // 获取查询字段（自定义字段优先）
                $keysInfo = pathinfo(is_array($val) ? $val[1] : $key);
                $isWhereHas = isset($keysInfo['extension']);
                // 自定义查询
                if ($val instanceof \Closure) {
                    call_user_func($val, $query, $search_input);
                    continue; // 进入下一个查询参数
                }

                // 操作符号
                $operator = is_array($val) ? $val[0] : $val; // =, >, >=, <,<=, like, endLike, startLike, in
                if ('in' === $operator) {
                    // in 查询
                    $search_input = is_array($search_input) ? $search_input : explode(',', $search_input);
                    if ($isWhereHas) {
                        $query->whereHas($keysInfo['filename'], function ($q) use ($keysInfo, $search_input) {
                            $q->whereIn($keysInfo['extension'], $search_input);
                        });
                    } else {
                        $query->whereIn($keysInfo['filename'], $search_input);
                    }
                } elseif ('between' === $operator) { // between查询
                    $search_input = is_array($search_input) ? $search_input : explode(',', $search_input);
                    if ($isWhereHas) {
                        $query->whereHas($keysInfo['filename'], function ($q) use ($keysInfo, $search_input) {
                            $q->whereBetween($keysInfo['extension'], $search_input);
                        });
                    } else {
                        $query->whereBetween($keysInfo['filename'], $search_input);
                    }
                } else { // where查询
                    if ('like' === $operator) {
                        $search_input = "%{$search_input}%";
                    } elseif ('endLike' === $operator) {
                        $search_input = "%{$search_input}";
                    } elseif ('startLike' === $operator) {
                        $search_input = "{$search_input}%";
                    }

                    // 执行查询
                    if ($isWhereHas) {
                        $query->whereHas($keysInfo['filename'], function ($q) use ($keysInfo, $operator, $search_input) {
                            $q->where($keysInfo['extension'], $operator, $search_input);
                        });
                    } else {
                        $query->where($keysInfo['filename'], $operator, $search_input);
                    }
                }
            }
        });
    }

    /**
     * 渲染json.
     */
    public function render(): array
    {
        $this->builderCallback && call_user_func($this->builderCallback, $this);
        $column = [];
        foreach ($this->column as $col) {
            $column[] = $col->render();
        }

        if ($this->tableOptions['showActions']) {
            // 行操作
            $column[] = [
                'bind' => [
                    'label' => '操作',
                    'prop' => '__actions__',
                    'width' => $this->tableOptions['actionWidth'],
                    'fixed' => 'right',
                ],
                'custom' => true,
            ];
        }

        foreach (['detailOptions', 'createOptions', 'editOptions'] as $key) {
            if ('drawer' === $this->$key['__type__']) {
                $this->$key['size'] = $this->$key['width'];
                unset($this->$key['width']);
            }
        }

        return [
            'filter' => $this->filter->render(),
            'table' => [
                'column' => $column,
                'tableOption' => $this->tableOptions,
                'detailOption' => $this->detailOptions,
                'editOption' => $this->editOptions,
                'createOption' => $this->createOptions,
                'bind' => $this->binds,
            ],
        ];
    }
}
