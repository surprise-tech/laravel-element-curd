<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/18
 */

namespace Wyz\ElementCurd\Form;

use Illuminate\Contracts\Support\Renderable;
use Wyz\ElementCurd\ElementAttributes;

class FormItem extends ElementAttributes implements Renderable
{
    /**
     * 类型.
     */
    protected string $type = 'text';

    /**
     * 字段配置.
     */
    protected array $binds = [];

    /**
     * label.
     */
    protected string $label = '';

    /**
     * 字段名称.
     */
    protected string $column = '';

    /**
     * placeholder.
     */
    protected string|null $placeholder = '';

    // 默认值
    protected mixed $defaultVal = null;

    /**
     * 表单验证规则.
     */
    public array $rules = [];

    /**
     * 表单验证提示语.
     */
    public array $message = [];

    /**
     * 必填错误信息.
     */
    protected string $requiredFormat = '%label';

    /**
     * placeholder 格式.
     */
    protected string $placeholderFormat = '%label';

    /**
     * 字段保存前回调.
     */
    protected \Closure $savingCallback;

    /**
     * 初始化.
     */
    public function __construct(string $column, string $label = '')
    {
        $this->savingCallback = fn ($v) => $v;
        $this->column = $column;
        $this->label = $label;
    }

    /**
     * 设置字段placeholder.
     */
    public function placeholder($placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * 设置宽度.
     */
    public function width(string $width): static
    {
        $this->binds['style'] = $this->binds['style'] ?? [];
        $this->binds['style']['width'] = $width;

        return $this;
    }

    /**
     * 设置默认值.
     */
    public function default($value): static
    {
        $this->defaultVal = $value;

        return $this;
    }

    /**
     * 设置表单验证规则.
     *
     * @param null $rules
     *
     * @return FormItem
     */
    public function rules($rules = null, array $messages = []): static
    {
        $this->rules = is_array($rules) ? $rules : array_filter(explode('|', $rules));
        $this->message = $messages;

        return $this;
    }

    /**
     * 获取字段名.
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * 获取默认值.
     */
    public function getDefault(): mixed
    {
        return $this->defaultVal;
    }

    /**
     * 转换处理请求值.
     */
    public function saving(\Closure $callback): static
    {
        $this->savingCallback = $callback;

        return $this;
    }

    /**
     * 获取保存前回调函数.
     */
    public function getSavingCallback(): \Closure
    {
        return $this->savingCallback;
    }

    /**
     * 转换前端验证规则.
     *
     * @return array[]
     */
    protected function getAsyncValidatorRules(): array
    {
        $required = array_filter($this->rules, fn ($rule) => str_starts_with((string) $rule, 'required'));
        if (count($required)) {
            return [
                [
                    'required' => true,
                    'message' => $this->message['required'] ?? str_replace(
                        '%label', $this->label, $this->requiredFormat),
                ],
            ];
        }

        return [];
    }

    /**
     * 渲染.
     */
    public function render(): array
    {
        if (!is_null($this->placeholder)) {
            $this->binds['placeholder'] = $this->placeholder ?: str_replace('%label', $this->label, $this->placeholderFormat);
        }

        return array_merge([
            'type' => $this->type,
            'name' => str_replace('.', '_', $this->column),
            'label' => $this->label,
            'bind' => $this->binds,
            'rules' => $this->getAsyncValidatorRules(),
            '__default__' => $this->defaultVal,
        ], $this->config);
    }
}
