<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/18
 */

namespace Wyz\ElementCurd\Form;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Wyz\ElementCurd\ElementAttributes;
use Wyz\ElementCurd\Enums\FormMode;

/**
 * Class Form.
 *
 * @method Text               text($column, $label = '')
 * @method Image              image($column, $label = '')
 * @method File               file($column, $label = '')
 * @method Textarea           textarea($column, $label = '')
 * @method Password           password($column, $label = '')
 * @method DatePicker         datePicker($column, $label = '')
 * @method Select             select($column, $label = '')
 * @method MultipleSelect     multipleSelect($column, $label = '')
 * @method TreeSelect         treeSelect($column, $label = '')
 * @method TreeMultipleSelect treeMultipleSelect($column, $label = '')
 * @method SwitchField        switch ($column, $label = '')
 */
class Form extends ElementAttributes implements Renderable
{
    use Macroable {
        __call as macroCall;
    }

    protected array $binds = [
        'label-width' => '80px',
    ];

    /**
     * 操作model.
     */
    protected mixed $model = null;

    /**
     * 操作model的ID.
     */
    protected mixed $_id = null;

    /**
     * 当前form类型.
     */
    protected FormMode|null $mode = null;

    /**
     * 表单项目.
     */
    protected array $formItems = [];

    /**
     * 回调函数.
     */
    protected \Closure|null $builderCallback = null;

    /**
     * 表单事件.
     */
    protected array $formEvent = [];

    public static array $formItemClass = [
        'text' => Text::class,
        'textarea' => Textarea::class,
        'password' => Password::class,
        'datePicker' => DatePicker::class,
        'select' => Select::class,
        'treeSelect' => TreeSelect::class,
        'multipleSelect' => MultipleSelect::class,
        'treeMultipleSelect' => TreeMultipleSelect::class,
        'switch' => SwitchField::class,
        'image' => Image::class,
        'file' => File::class,
    ];

    /**
     * 初始化.
     *
     * @param $model
     */
    public function __construct($model, ?\Closure $builderCallback = null)
    {
        $this->model = is_string($model) ? app($model) : $model;
        $this->mode = FormMode::MODE_CREATE; // 默认创建模式
        $this->builderCallback = $builderCallback;
    }

    /**
     * Create a form instance.
     *
     * @param mixed ...$params
     *
     * @return $this
     */
    public static function make(...$params): static
    {
        return new static(...$params);
    }

    // 生成允许的字段
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        $className = static::$formItemClass[$method];
        $column = Arr::get($parameters, 0, '');

        $element = new $className($column, Arr::get($parameters, 1, ''));

        $this->formItems[$column] = $element;

        return $element;
    }

    /**
     * 设置form类型.
     */
    public function setMode(FormMode $formMode): static
    {
        $this->mode = $formMode;

        return $this;
    }

    /**
     * 是否是创建.
     */
    public function isCreate(): bool
    {
        return FormMode::MODE_CREATE === $this->mode;
    }

    /**
     * 是否是编辑.
     */
    public function isEdit(): bool
    {
        return FormMode::MODE_EDIT === $this->mode;
    }

    /**
     * 设置key.
     */
    public function setKey($id): static
    {
        $this->_id = $id;

        return $this;
    }

    /**
     * 保存前回调函数.
     */
    public function saving(?\Closure $callback = null): static
    {
        $this->formEvent['saving'] = $callback;

        return $this;
    }

    /**
     * 保存.
     */
    public function save()
    {
        $model = null;
        if ($this->_id) {
            $model = $this->model->findOrFail($this->_id);
        }
        $this->builderCallback && call_user_func($this->builderCallback, $this, $model);
        $ipt = request();
        [$rules, $message] = $this->getRules();
        $ipt->validate($rules, $message);
        // 当前model数据
        $data = [];
        // 关联数据
        $withToData = [];
        foreach (array_keys($this->formItems) as $key) {
            if (Str::contains($key, '.')) {
                $withToData[$key] = $ipt->input(str_replace('.', '_', $key));
            } else {
                $data[$key] = $ipt->input($key);
            }
        }
        if (isset($this->formEvent['saving']) && $this->formEvent['saving'] instanceof \Closure) {
            $data = call_user_func($this->formEvent['saving'], $this, $data, $ipt);
        }

        if ($this->_id && $model) { // 更新
            foreach ($data as $key => $val) {
                $model->$key = $val;
            }
            $model->save();
        } else {
            // 创建
            $model = $this->model->create($data);
        }

        // 保存后回调
        if (isset($this->formEvent['saved']) && $this->formEvent['saved'] instanceof \Closure) {
            call_user_func($this->formEvent['saved'], $this, $model, $withToData);
        }

        return $model;
    }

    /**
     * 保存后回调函数.
     */
    public function saved(?\Closure $callback = null): static
    {
        $this->formEvent['saved'] = $callback;

        return $this;
    }

    /**
     * 删除前回调.
     */
    public function deleting(?\Closure $callback = null): static
    {
        $this->formEvent['deleting'] = $callback;

        return $this;
    }

    /**
     * 删除后回调.
     */
    public function deleted(?\Closure $callback = null): static
    {
        $this->formEvent['deleted'] = $callback;

        return $this;
    }

    /**
     * 获取字段表单验证规则.
     */
    public function getRules(): array
    {
        $rules = [];
        $message = [];
        foreach ($this->formItems as $key => $formItem) {
            $rules[$key] = $formItem->rules;
            $message = array_merge($message, $formItem->message);
        }

        return [$rules, $message];
    }

    /**
     * 删除.
     */
    public function delete()
    {
        $model = $this->model->where('id', $this->_id)->first();
        // 删除前
        if (isset($this->formEvent['deleting']) && $this->formEvent['deleting'] instanceof \Closure) {
            call_user_func($this->formEvent['deleting'], $this, $model);
        }
        // 删除
        $res = $model->delete();

        // 删除后
        if (isset($this->formEvent['deleted']) && $this->formEvent['deleted'] instanceof \Closure) {
            call_user_func($this->formEvent['deleted'], $this, $this->_id);
        }

        return $res;
    }

    /**
     * 设置LabelWidth.
     */
    public function labelWidth(string $width): static
    {
        $this->binds['label-width'] = $width;

        return $this;
    }

    /**
     * 渲染.
     */
    public function render(): array
    {
        $fields = [];
        if ($this->_id) {
            $model = $this->model->findOrFail($this->_id);
        }
        $this->builderCallback && call_user_func($this->builderCallback, $this, $model ?? $this->model);
        foreach ($this->formItems as $formItem) {
            if ($this->isEdit() && isset($model)) {
                // 编辑的情况下将数据库中的值 设置为默认值
                $fields[] = $formItem->default(data_get($model, $formItem->getColumn()))->render();
                continue;
            }
            if ($this->isCreate()) { // 创建的情况下直接渲染
                $fields[] = $formItem->render();
            }
        }

        return array_merge([
            'fields' => $fields,
            'bind' => $this->binds,
        ], $this->config);
    }
}
