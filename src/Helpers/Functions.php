<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Helpers;

if (! function_exists('human_readable_file_size')) {
    /**
     * @param  int  $bytes
     * @param  int  $decimals
     * @return string
     */
    function human_readable_file_size(int $bytes, int $decimals = 2): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }

        $factor = floor(log($bytes, 1024));

        return sprintf("%.{$decimals}f ", $bytes / pow(1024, $factor)).[
                'B',
                'KB',
                'MB',
                'GB',
                'TB',
                'PB',
                'EB',
                'ZB',
            ][$factor];
    }
}

if (! function_exists('compare_float_value')) {
    /**
     * @param  float  $a
     * @param  float  $b
     * @param  string  $operator
     * @param  float  $epsilon
     * @return bool
     */
    function compare_float_value(float $a, float $b, string $operator = '===', float $epsilon = 0.00001): bool
    {
        switch ($operator) {
            case '==':
            case '===':
                if (abs($a - $b) < $epsilon) {
                    return true;
                }
                break;

            case "!=":
            case '!==':
                if (abs($a - $b) > $epsilon) {
                    return true;
                }
                break;

            case "<":
                if (abs($a - $b) < $epsilon) {
                    return false;
                } else {
                    if ($a < $b) {
                        return true;
                    }
                }
                break;

            case ">":
                if (abs($a - $b) < $epsilon) {
                    return false;
                } else {
                    if ($a > $b) {
                        return true;
                    }
                }
                break;

            case "<=":
                if (compare_float_value($a, $b, '<') || compare_float_value($a, $b, '==')) {
                    return true;
                }
                break;

            case ">=":
                if (compare_float_value($a, $b, '>') || compare_float_value($a, $b, '==')) {
                    return true;
                }
                break;
            default:
                return false;
        }

        return false;
    }
}

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
