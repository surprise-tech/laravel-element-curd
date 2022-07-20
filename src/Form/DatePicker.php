<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/18
 */

namespace Wyz\ElementCurd\Form;

class DatePicker extends FormItem
{
    protected string $type = 'el-date-picker';

    protected string $placeholderFormat = '请选择%label';

    protected array $binds = [
        'type' => 'date',
        'value-format' => 'YYYY-MM-DD',
    ];

    protected bool $enableTime = false;

    /**
     * 开启时间选择器.
     */
    public function enableTimePicker(bool $flag = true): static
    {
        $this->enableTime = $flag;
        $this->binds = $flag ? [
            'type' => 'date',
            'value-format' => 'YYYY-MM-DD',
        ] : [
            'type' => 'datetime',
            'value-format' => 'YYYY-MM-DD hh:mm:ss',
        ];

        return $this;
    }

    /**
     * 区间选择器.
     */
    public function between()
    {
        if ($this->enableTime) {
            $this->binds['type'] = 'datetimerange';
            $this->binds['start-placeholder'] = '开始时间';
            $this->binds['end-placeholder'] = '结束时间';
        } else {
            $this->binds['type'] = 'daterange';
            $this->binds['start-placeholder'] = '开始日期';
            $this->binds['end-placeholder'] = '结束日期';
        }

        return $this;
    }
}
