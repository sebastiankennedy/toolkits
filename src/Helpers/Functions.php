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
    function compare_float_value(float $a, float $b, string $operator = '===', $epsilon = 0.00001): bool
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
