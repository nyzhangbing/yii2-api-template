<?php

namespace app\core;


class GridDataSource
{
    public $items;
    public $total;

    function __construct($items, $total)
    {
        $this->items = $items;
        $this->total = (int)$total;
    }
}