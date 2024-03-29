<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Algorithms\Sorting;

use Luyiyuan\Toolkits\Helpers\ArrayHelper;

/**
 * 冒泡排序
 */
class BubbleSort
{
    /**
     * @param  array<mixed>  $array
     * @param  string  $order
     * @return void
     */
    public static function sort(array &$array, string $order = 'asc'): void
    {
        $n = count($array);
        // n 个数排序，第一轮只用进行 n-1 趟
        for ($i = 0; $i < $n - 1; $i++) {
            // 第二轮遍历开始时，最大值已在正确位置上，还剩 n-1 个元素需要排列，那只用进行 n-2 轮
            for ($j = 0; $j < $n - 1 - $i; $j++) {
                if ($order === 'asc') {
                    if ($array[$j] > $array[$j + 1]) {
                        ArrayHelper::swap($array, $j, $j + 1);
                    }
                } else {
                    if ($array[$j] < $array[$j + 1]) {
                        ArrayHelper::swap($array, $j, $j + 1);
                    }
                }
            }
        }
    }

    /**
     * @param  string  $filed
     * @param  array<mixed>  $array
     * @param  string  $order
     * @return void
     */
    public static function sortBy(string $filed, array &$array, string $order = 'asc'): void
    {
        $n = count($array);
        for ($i = 0; $i < $n - 1; $i++) {
            for ($j = 0; $j < $n - 1 - $i; $j++) {
                if ($order === 'asc') {
                    if ($array[$j][$filed] > $array[$j + 1][$filed]) {
                        ArrayHelper::swap($array, $j, $j + 1);
                    }
                } else {
                    if ($array[$j][$filed] < $array[$j + 1][$filed]) {
                        ArrayHelper::swap($array, $j, $j + 1);
                    }
                }
            }
        }
    }
}
