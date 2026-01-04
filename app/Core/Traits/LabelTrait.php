<?php

namespace App\Core\Traits;

trait LabelTrait
{
    function dueToLabels($label = null)
    {
        $labels = [
            'low_stock' => 'Low Stock',
            'out_of_stock' => 'Out of Stock',
            'discontinued' => 'Discontinued',
        ];
        return $label ? $labels[$label] : $labels;
    }
}
