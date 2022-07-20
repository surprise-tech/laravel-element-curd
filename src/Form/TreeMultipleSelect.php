<?php

namespace Wyz\ElementCurd\Form;

class TreeMultipleSelect extends TreeSelect
{
    public function __construct(string $column, string $label = '')
    {
        parent::__construct($column, $label);
        $this->binds['multiple'] = true;
        $this->binds['show-checkbox'] = true;
    }
}
