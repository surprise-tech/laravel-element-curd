<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/18
 */

namespace Wyz\ElementCurd\Grid;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Macroable;
use Wyz\ElementCurd\Form\Cascader;
use Wyz\ElementCurd\Form\DatePicker;
use Wyz\ElementCurd\Form\Form;
use Wyz\ElementCurd\Form\MultipleSelect;
use Wyz\ElementCurd\Form\Password;
use Wyz\ElementCurd\Form\Select;
use Wyz\ElementCurd\Form\SwitchField;
use Wyz\ElementCurd\Form\Text;
use Wyz\ElementCurd\Form\Textarea;
use Wyz\ElementCurd\Form\TreeMultipleSelect;
use Wyz\ElementCurd\Form\TreeSelect;

/**
 * Class Filter.
 *
 * @method Text               text($column, $label = '', $searchRule = '')
 * @method Textarea           textarea($column, $label = '', $searchRule = '')
 * @method Password           password($column, $label = '', $searchRule = '')
 * @method DatePicker         datePicker($column, $label = '', $searchRule = '')
 * @method Select             select($column, $label = '', $searchRule = '')
 * @method MultipleSelect     multipleSelect($column, $label = '', $searchRule = '')
 * @method TreeSelect         treeSelect($column, $label = '', $searchRule = '')
 * @method TreeMultipleSelect treeMultipleSelect($column, $label = '', $searchRule = '')
 * @method SwitchField        switch ($column, $label = '', $searchRule = '')
 * @method Cascader           cascader ($column, $label = '', $searchRule = '')
 */
class Filter implements Renderable
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * 搜索字段.
     */
    protected array $searchField = [];

    /**
     * 搜索规则.
     */
    public array $searchRule = [];

    // 生成允许的字段
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        $className = Form::$formItemClass[$method];
        $column = Arr::get($parameters, 0, '');

        $element = new $className($column, Arr::get($parameters, 1, ''));
        $searchRule = Arr::get($parameters, 2, null);
        $this->searchField[] = $element;
        if ($searchRule) {
            $this->searchRule[$column] = $searchRule;
        }

        return $element;
    }

    /**
     * 渲染.
     */
    public function render(): array
    {
        $res = [];
        foreach ($this->searchField as $field) {
            $res[] = $field->render();
        }

        return $res;
    }
}
