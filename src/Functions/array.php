<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Functions;

use InvalidArgumentException;

if (!function_exists('compare_grade')) {
    /**
     * @param string|null $a
     * @param string|null $b
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

if (!function_exists('rank')) {
    /**
     * @param array<mixed> $rows
     * @param string $compareField
     * @param int $precision
     * @param string $rankField
     * @param string $rankBaseField
     * @return array<mixed>
     *
     * @link https://www.php.net/manual/zh/array.sorting.php
     */
    function rank(
        array  $rows,
        string $compareField = 'score',
        string $rankField = 'rank',
        string $rankBaseField = 'rank_base',
        array  $specialValues = ['缺考', null],
        int    $precision = 2
    ): array
    {
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

if (!function_exists('array_index_by')) {
    /**
     * 自定义索引数组
     *
     * @param array<mixed> $items
     * @param array<mixed>|string|int|callable $key
     * @return array<mixed>
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

if (!function_exists('value_of_key')) {
    /**
     * 灵活获取键的值
     *
     * @param array<mixed>|object $item
     * @param array<mixed>|int|string $key
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

if (!function_exists('array_order_by')) {
    /**
     * 多字段排序
     * @param mixed $args
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

if (!function_exists('array_sort_by_keys')) {
    function array_sort_by_keys(array $keys, array $array): array
    {
        $sortedArray = [];
        foreach ($array as $data) {
            uksort($data, function ($a, $b) use ($keys) {
                $posA = array_search($a, $keys);
                $posB = array_search($b, $keys);

                if ($posA === false) {
                    $posA = PHP_INT_MAX;
                }
                if ($posB === false) {
                    $posB = PHP_INT_MAX;
                }

                return $posA - $posB;
            });
            $sortedArray[] = $data;
        }
        return $sortedArray;
    }
}

if (!function_exists('is_subset_of_set')) {
    /**
     * 是否为子集
     *
     * @param array<mixed> $subset
     * @param array<mixed> $set
     * @return bool
     */
    function is_subset_of_set(array $subset, array $set): bool
    {
        return empty(array_diff($subset, $set));
    }
}

if (!function_exists('is_intersection_of_set')) {
    /**
     * 是否为交集
     *
     * @param array<mixed> $intersection
     * @param array<mixed> $set
     * @return bool
     */
    function is_intersection_of_set(array $intersection, array $set): bool
    {
        return !empty(array_intersect($intersection, $set));
    }
}

if (!function_exists('is_difference_of_set')) {
    /**
     * 是否为差集
     *
     * @param array<mixed> $difference
     * @param array<mixed> $set
     * @return bool
     */
    function is_difference_of_set(array $difference, array $set): bool
    {
        return !empty(array_diff($difference, $set));
    }
}

if (!function_exists('array_swap')) {
    /**
     * 交换数组元素
     *
     * @param array<mixed> $data
     * @param int|string $i
     * @param int|string $j
     * @return void
     */
    function array_swap(array &$data, $i, $j): void
    {
        $tmp = $data[$i];
        $data[$i] = $data[$j];
        $data[$j] = $tmp;
    }
}

if (!function_exists('array_combine_values')) {
    function array_combine_values($keys, $values): array
    {
        foreach ($values as &$value) {
            $value = array_combine($keys, $value);
        }
        unset($value);

        return array_values($values);
    }
}

if (!function_exists('array_remap_keys')) {
    function array_remap_keys($keysMapping, $array): array
    {
        $remappedArray = [];
        foreach ($array as $item) {
            foreach ($item as $key => $value) {
                if (isset($keysMapping[$key])) {
                    $newKey = $keysMapping[$key];
                    $item[$newKey] = $value;
                    unset($item[$key]);
                }
            }
            $remappedArray[] = $item;
        }
        return $remappedArray;
    }
}

if (!function_exists('array_unset_keys')) {
    function array_unset_keys($keys, &$array)
    {
        foreach ($array as &$data) {
            foreach ($data as $key => $value) {
                if (in_array($key, $keys)) {
                    unset($data[$key]);
                }
            }
        }
    }
}

if (!function_exists('array_sum_columns_to_new_field')) {
    function array_sum_columns_to_new_field(array &$array, array $columns, string $newField)
    {
        foreach ($array as &$data) {
            $sum = 0;
            foreach ($columns as $key) {
                if (isset($data[$key])) {
                    $sum += (float)$data[$key];
                }
            }
            $data[$newField] = (string)$sum;
        }
        unset($data);
    }
}

if (!function_exists('array_group_by')) {
    function array_group_by(array $array, string $groupBy): array
    {
        $groupedArray = [];
        foreach ($array as $item) {
            if (isset($item[$groupBy])) {
                $groupedArray[$item[$groupBy]][] = $item;
            }
        }
        return $groupedArray;
    }
}
if (!function_exists('array_fill_missing_keys')) {
    function array_fill_missing_keys(array $keys, array $array, ?string $value = null)
    {
        $filledArray = [];
        foreach ($array as $row => $data) {
            foreach ($keys as $key) {
                if (!array_key_exists($key, $data)) {
                    $data[$key] = $value;
                    $filledArray[$row] = $data;
                }
            }
        }
        return $filledArray;
    }
}