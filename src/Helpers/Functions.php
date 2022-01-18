<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Helpers;

use InvalidArgumentException;
use RuntimeException;

if (! function_exists('human_readable_file_size')) {
    /**
     * @param  int  $bytes
     * @param  int  $decimals
     * @return string
     */
    function human_readable_file_size(int $bytes, int $decimals = 2): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        $factor = floor(log($bytes, 1024));

        return sprintf("%.{$decimals}f ", $bytes / pow(1024, $factor)) . [
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

if (! function_exists('double')) {
    /**
     * @param  int|float  $value
     * @return int|float
     */
    function double($value)
    {
        if (is_int($value) || is_float($value)) {
            return $value * 2;
        }

        throw new InvalidArgumentException('The value must be an integer or float.');
    }
}

if (! function_exists('fail_if_file_not_exists')) {
    /**
     * 文件不存在则抛出异常
     *
     * @param  string  $file
     * @return void
     */
    function fail_if_file_not_exists(string $file): void
    {
        if (! file_exists($file)) {
            throw new InvalidArgumentException("The file $file not exists.");
        }
    }
}

if (! function_exists('fail_if_file_not_readable')) {
    /**
     * 文件不可读则抛出异常
     *
     * @param  string  $file
     * @return void
     */
    function fail_if_file_not_readable(string $file): void
    {
        if (! is_readable($file)) {
            throw new InvalidArgumentException("The file $file is not readable.");
        }
    }
}

if (! function_exists('fail_if_not_file')) {
    /**
     * 文件不是正常文件则抛出异常
     *
     * @param $file
     * @return void
     */
    function fail_if_not_file($file): void
    {
        fail_if_file_not_exists($file);

        if (! is_file($file)) {
            throw new InvalidArgumentException("The file $file is not a file.");
        }
    }
}

if (! function_exists('fail_if_not_dir')) {
    /**
     * 如果文件不是一个目录则抛出异常
     *
     * @param $file
     * @return void
     */
    function fail_if_not_dir($file): void
    {
        fail_if_file_not_exists($file);
        if (! is_dir($file)) {
            throw new InvalidArgumentException("The file $file is not a dir.");
        }
    }
}

if (! function_exists('fail_if_file_get_no_contents')) {
    /**
     * 如果文件获取不到内容则抛出异常
     *
     * @param $file
     * @return string
     */
    function fail_if_file_get_no_contents($file): string
    {
        // 判断是否为本地文件
        if (filter_var($file, FILTER_VALIDATE_URL) === false) {
            fail_if_file_not_exists($file);
            fail_if_file_not_readable($file);
        }

        $data = file_get_contents($file);
        if ($data === false) {
            throw new RuntimeException("failed to get contents from file $file");
        }

        return $data;
    }
}


