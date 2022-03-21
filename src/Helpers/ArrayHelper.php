<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Helpers;

use Illuminate\Support\Arr;

use function Luyiyuan\Toolkits\Functions\array_swap;

class ArrayHelper extends Arr
{
    /**
     * @param  array  $data
     * @param  int|string  $i
     * @param  int|string  $j
     * @return void
     */
    public static function swap(array &$data, $i, $j): void
    {
        array_swap($data, $i, $j);
    }
}
