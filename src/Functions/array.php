<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Functions;

use InvalidArgumentException;

if (! function_exists('compare_grade')) {
    /**
     * @param  string|null  $a
     * @param  string|null  $b
     * @return int
     */
    function compare_grade(?string $a, ?string $b): int
    {
        $orders = array_flip([
            '一年级',
            '二年级',
            '三年级',
            '四年级',
            '五年级',
            '六年级',
            '初一',
            '七年级',
            '初二',
            '八年级',
            '初三',
            '九年级',
            '高一',
            '高二',
            '高三',
        ]);

        if ($a === $b) {
            return 0;
        }

        $orderOfA = $orders[$a] ?? 99999;
        $orderOfB = $orders[$b] ?? 99999;

        return $orderOfA <=> $orderOfB;
    }
}

if (! function_exists('rank')) {
    /**
     * @param  array  $rows
     * @param  string  $compareField
     * @param  string  $rankField
     * @param  string  $rankBaseField
     * @param  array  $specialValues
     * @param  int  $precision
     * @return array
     *
     * @link https://www.php.net/manual/zh/array.sorting.php
     */
    function rank(
        array $rows,
        string $compareField = 'score',
        string $rankField = 'rank',
        string $rankBaseField = 'rank_base',
        array $specialValues = ['缺考', null, ''],
        int $precision = 2
    ): array {
        uasort($rows, function ($a, $b) use ($compareField, $specialValues, $precision) {
            $valueA = is_array($a) ? $a[$compareField] : $a->$compareField;
            $valueB = is_array($b) ? $b[$compareField] : $b->$compareField;

            // 检查是否为特殊值
            $isSpecialValueA = in_array($valueA, $specialValues, true);
            $isSpecialValueB = in_array($valueB, $specialValues, true);

            // 如果两个值都是特殊值，认为它们相等
            if ($isSpecialValueA && $isSpecialValueB) {
                return 0;
            }

            // 如果只有 A 是特殊值，则 B 应该排在 A 前面
            if ($isSpecialValueA) {
                return 1;
            }

            // 如果只有 B 是特殊值，则 A 应该排在 B 前面
            if ($isSpecialValueB) {
                return -1;
            }

            // 如果两个值都不是特殊值，使用 bccomp 进行比较
            return bccomp($valueB, $valueA, $precision);
        });

        $isArray = is_array($rows[0]);
        $rankBase = count($rows);
        $prevScore = (string)PHP_INT_MIN;
        $rank = null;
        $no = 1;

        foreach ($rows as &$row) {
            $currentScore = $isArray ? $row[$compareField] : $row->$compareField;
            // 特殊值无需计算排名
            if (in_array($currentScore, $specialValues)) {
                continue;
            }
            if (bccomp($prevScore, $currentScore, $precision) !== 0) {
                $rank = $no;
                $prevScore = $currentScore;
            }

            $isArray ? $row[$rankField] = $rank : $row->$rankField = $rank;
            // 存在无需排名基数的情况
            if ($rankBaseField) {
                $isArray ? $row[$rankBaseField] = $rankBase : $row->$rankBaseField = $rankBase;
            }

            $no++;
        }


        return $rows;
    }
}

if (! function_exists('array_index_by')) {
    /**
     * 自定义索引数组
     *
     * @param  array  $items
     * @param  array|string|int|callable  $key
     * @return array
     */
    function array_index_by(array $items, $key): array
    {
        if (is_string($key) || is_int($key)) {
            return array_column($items, null, $key);
        }

        $data = [];
        foreach ($items as $index => $item) {
            if (is_array($key)) {
                $valueOfKey = value_of_key($item, $key);
            } elseif (is_callable($key)) {
                $valueOfKey = $key($item, $index);
            } else {
                throw new InvalidArgumentException('unsupported key type.');
            }
            $data[$valueOfKey] = $item;
        }

        return $data;
    }
}

if (! function_exists('value_of_key')) {
    /**
     * 灵活获取键的值
     *
     * @param  array|object  $item
     * @param  array|int|string  $key
     * @return mixed
     */
    function value_of_key($item, $key)
    {
        if (is_object($item)) {
            return $item->$key;
        }

        if (is_array($key)) {
            if (sizeof($key) === 1) {
                return $item[$key[0]];
            }

            $current = array_shift($key);

            return value_of_key($item[$current], $key);
        }

        return $item[$key];
    }
}

if (! function_exists('array_order_by')) {
    /**
     * 多字段排序
     * @param  mixed  $args
     * @return mixed|null
     * @example
     * $data[] = array('volume' => 67, 'edition' => 2);
     * $data[] = array('volume' => 86, 'edition' => 1);
     * $data[] = array('volume' => 85, 'edition' => 6);
     * $data[] = array('volume' => 98, 'edition' => 2);
     * $data[] = array('volume' => 86, 'edition' => 6);
     * $data[] = array('volume' => 67, 'edition' => 7);
     *
     * $sorted = array_order_by($data, 'volume', SORT_DESC, 'edition', SORT_ASC);
     *
     */
    function array_order_by(...$args)
    {
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row) {
                    $tmp[$key] = $row[$field];
                }
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);

        return array_pop($args);
    }
}

if (! function_exists('array_sort_by_keys')) {
    /**
     * @param  array  $array
     * @param  array  $keys
     * @return array
     */
    function array_sort_by_keys(array $array, array $keys): array
    {
        // 获取键的位置映射
        $keyPositions = array_flip($keys);
        $sortedArray = [];
        foreach ($array as $data) {
            uksort($data, function ($a, $b) use ($keyPositions) {
                $posA = $keyPositions[$a] ?? PHP_INT_MAX;
                $posB = $keyPositions[$b] ?? PHP_INT_MAX;
                return $posA <=> $posB;
            });
            $sortedArray[] = $data;
        }

        return $sortedArray;
    }
}

if (! function_exists('is_subset_of_set')) {
    /**
     * 是否为子集
     *
     * @param  array  $subset
     * @param  array  $set
     * @return bool
     */
    function is_subset_of_set(array $subset, array $set): bool
    {
        return empty(array_diff($subset, $set));
    }
}

if (! function_exists('is_intersection_of_set')) {
    /**
     * 是否为交集
     *
     * @param  array  $intersection
     * @param  array  $set
     * @return bool
     */
    function is_intersection_of_set(array $intersection, array $set): bool
    {
        return ! empty(array_intersect($intersection, $set));
    }
}

if (! function_exists('is_difference_of_set')) {
    /**
     * 是否为差集
     *
     * @param  array  $difference
     * @param  array  $set
     * @return bool
     */
    function is_difference_of_set(array $difference, array $set): bool
    {
        return ! empty(array_diff($difference, $set));
    }
}

if (! function_exists('array_swap')) {
    /**
     * 交换数组元素
     *
     * @param  array  $data
     * @param  int|string  $i
     * @param  int|string  $j
     * @return void
     */
    function array_swap(array &$data, $i, $j): void
    {
        $tmp = $data[$i];
        $data[$i] = $data[$j];
        $data[$j] = $tmp;
    }
}

if (! function_exists('array_combine_values')) {
    /**
     * 通过一个键值数组将一个关联数组组成成为一个索引数组
     *
     * @param  array  $keys
     * @param  array  $values
     * @return array
     */
    function array_combine_values(array $keys, array $values): array
    {
        foreach ($values as &$value) {
            if (count($value) !== count($keys)) {
                throw new InvalidArgumentException('The number of keys must match the number of values');
            }
            $value = array_combine($keys, $value);
        }
        unset($value);

        return array_values($values);
    }
}

if (! function_exists('array_remap_keys')) {
    /**
     * 遍历一个数组的每个子数组，根据提供的键名映射关系数组，重新映射子数组中的键名，并返回包含所有重新映射后的子数组的新数组。
     *
     * @param  array  $array
     * @param  array  $keysMapping
     * @return array
     */
    function array_remap_keys(array $array, array $keysMapping): array
    {
        $remappedArray = [];
        foreach ($array as $item) {
            $newItem = [];
            foreach ($item as $key => $value) {
                // 直接使用新键，如果没有映射则使用原键
                $newKey = $keysMapping[$key] ?? $key;
                if (! isset($newItem[$newKey]) || $newKey !== $key) {
                    $newItem[$newKey] = $value;
                }
            }
            $remappedArray[] = $item;
        }

        return $remappedArray;
    }
}

if (! function_exists('array_unset_keys')) {
    function array_unset_keys(array &$array, array $keys)
    {
        $keys = array_flip($keys);
        foreach ($array as &$data) {
            foreach ($data as $key => $value) {
                if (isset($keys[$key])) {
                    unset($data[$key]);
                }
            }
        }
        unset($data);
    }
}

if (! function_exists('array_sum_columns_to_new_column')) {
    /**
     * 将指定列的值求和，并将结果作为新列添加到数组的每个元素中。
     *
     * @param  array  $array
     * @param  array  $columns
     * @param  string  $newColumn
     * @return void
     */
    function array_sum_columns_to_new_column(array &$array, array $columns, string $newColumn)
    {
        foreach ($array as &$data) {
            $sum = 0.0;
            foreach ($columns as $key) {
                if (isset($data[$key])) {
                    $sum += (float)$data[$key];
                }
            }
            $data[$newColumn] = $sum;
        }
        unset($data);
    }
}

if (! function_exists('array_group_by')) {
    function array_group_by(array $array, $groupByKey, bool $keepKey = false): array
    {
        if (! is_int($groupByKey) && ! is_string($groupByKey) && ! is_callable($groupByKey) && ! is_array($groupByKey)) {
            throw new InvalidArgumentException('unsupported key type');
        }

        $groupedArray = [];
        foreach ($array as $key => $item) {
            if (is_callable($groupByKey)) {
                $value = $groupByKey($item, $key);
            } else {
                $value = value_of_key($item, $key);
            }

            if ($keepKey) {
                $groupedArray[$value][$key] = $item;
            } else {
                $groupedArray[$value][] = $item;
            }
        }

        return $groupedArray;
    }
}
if (! function_exists('array_fill_missing_keys')) {
    /**
     * 为数组中缺失的键填充指定的值。
     *
     * @param  array  $array
     * @param  array  $keys
     * @param  mixed  $value
     * @return array
     */
    function array_fill_missing_keys(array $array, array $keys, $value = null): array
    {
        if (! is_numeric($value) || ! is_string($value) || ! is_null($value) || ! is_callable($value)) {
            throw new InvalidArgumentException('unsupported value.');
        }

        foreach ($array as $row => &$data) {
            foreach ($keys as $key) {
                if (! array_key_exists($key, $data)) {
                    if (is_callable($value)) {
                        $data[$key] = $value($row, $data);
                    } else {
                        $data[$key] = value_of_key($data, $value);
                    }
                }
            }
        }
        unset($data);

        return $array;
    }
}
