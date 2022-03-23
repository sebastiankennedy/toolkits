<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Algorithms\Sorting;

use Luyiyuan\Toolkits\Helpers\ArrayHelper;

/**
 * 快速排序
 */
class QuickSort
{
    /**
     * @link - 需要优化：https://blog.csdn.net/baidu_30000217/article/details/53312990
     *
     * @param  array<array>  $data
     * @param  int|null  $left
     * @param  int|null  $right
     * @return void
     */
    public static function sort(array &$data, ?int $left = null, ?int $right = null): void
    {
        $left = null === $left ? 0 : $left;
        $right = null === $right ? count($data) - 1 : $right;
        if ($left > $right) {
            return;
        }

        $i = $left;
        $j = $right;
        $pivot = $data[$left];
        while ($i !== $j) {
            // 先从右往左找比基准数小的
            while ($i < $j && $data[$j] >= $pivot) {
                $j--;
            }

            // 再从左往右找比基准数大的
            while ($i < $j && $data[$i] <= $pivot) {
                $i++;
            }

            // i 和 j 未相遇时
            if ($i < $j) {
                // 交换两个元素在数组中的位置
                ArrayHelper::swap($data, $i, $j);
            }
        }
        // 将基准数归位
        ArrayHelper::swap($data, $left, $i);

        self::sort($data, $left, $i - 1);
        self::sort($data, $i + 1, $right);
    }

    /**
     * @param  string  $field
     * @param  array[]  $data
     * @param  int|null  $left
     * @param  int|null  $right
     * @return void
     */
    public static function sortBy(string $field, array &$data, ?int $left = null, ?int $right = null): void
    {
        $left = null === $left ? 0 : $left;
        $right = null === $right ? count($data) - 1 : $right;
        if ($left > $right) {
            return;
        }

        $i = $left;
        $j = $right;
        $pivot = $data[$left][$field];
        while ($i !== $j) {
            while ($i < $j && $data[$j][$field] >= $pivot) {
                $j--;
            }

            while ($i < $j && $data[$i][$field] <= $pivot) {
                $i++;
            }

            if ($i < $j) {
                ArrayHelper::swap($data, $i, $j);
            }
        }
        ArrayHelper::swap($data, $left, $i);

        self::sortBy($field, $data, $left, $i - 1);
        self::sortBy($field, $data, $i + 1, $right);
    }
}
