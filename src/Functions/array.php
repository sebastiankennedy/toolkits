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
     * @param  array<mixed> $rows
     * @param  string  $compareField
     * @param  int  $precision
     * @param  string  $rankField
     * @param  string  $rankBaseField
     * @return array<mixed>
     *
     * @link https://www.php.net/manual/zh/array.sorting.php
     */
    function rank(
        array $rows,
        string $compareField = 'score',
        string $rankField = 'rank',
        string $rankBaseField = 'rank_base',
        int $precision = 2
    ): array {
        uasort($rows, fn ($a, $b): int => bccomp(
            is_array($b) ? $b[$compareField] : $b->$compareField,
            is_array($a) ? $a[$compareField] : $a->$compareField,
            $precision
        ));

        $isArray = is_array($rows[0]);
        $rankBase = count($rows);
        $prevScore = (string)PHP_INT_MIN;
        $rank = null;
        $no = 1;

        foreach ($rows as &$row) {
            $currentScore = $isArray ? $row[$compareField] : $row->$compareField;
            if (bccomp($prevScore, $currentScore, $precision) !== 0) {
                $rank = $no;
                $prevScore = $currentScore;
            }

            $isArray ? $row[$rankField] = $rank : $row->$rankField = $rank;
            $isArray ? $row[$rankBaseField] = $rankBase : $row->$rankBaseField = $rankBase;

            $no++;
        }


        return $rows;
    }
}

if (! function_exists('array_index_by')) {
    /**
     * 自定义索引数组
     *
     * @param  array<mixed>  $items
     * @param  array<mixed>|string|int|callable  $key
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

if (! function_exists('value_of_key')) {
    /**
     * 灵活获取键的值
     *
     * @param  array<mixed>|object  $item
     * @param  array<mixed>|int|string  $key
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

if (! function_exists('is_subset_of_set')) {
    /**
     * 是否为子集
     *
     * @param  array<mixed>  $subset
     * @param  array<mixed>  $set
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
     * @param  array<mixed>  $intersection
     * @param  array<mixed>  $set
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
     * @param  array<mixed>  $difference
     * @param  array<mixed>  $set
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
     * @param  array<mixed>  $data
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
