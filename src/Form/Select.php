<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/18
 */

namespace Wyz\ElementCurd\Form;

use Closure;

class Select extends FormItem
{
    protected string $type = 'cu-select';

    protected string $placeholderFormat = '请选择%label';

    protected array $binds = [
        'clearable' => true,
        'style' => [
            'width' => '100%',
        ],
    ];

    /**
     * 设置选项.
     */
    public function options($options, Closure $callback = null): static
    {
        $opts = [];
        foreach ($options as $key => $val) {
            $temp = [
                'value' => $key,
                'label' => $val,
            ];
            if ($callback) {
                $temp = call_user_func($callback, $temp);
            }
            $opts[] = $temp;
        }

        $this->binds = array_merge($this->binds, [
            'options' => $opts,
        ]);

        return $this;
    }
}
