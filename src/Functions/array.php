<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Functions;

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
     * @param  int  $precision
     * @param  string  $rankField
     * @param  string  $rankBaseField
     * @return array
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
