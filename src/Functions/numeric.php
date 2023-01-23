<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Functions;

use InvalidArgumentException;

if (! function_exists('compare_float_value')) {
    /**
     * 比较两个浮点数的大小
     *
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

if (! function_exists('double')) {
    /**
     * @param  int|float  $value
     * @return int|float
     */
    function double($value)
    {
        if (! is_int($value) || ! is_float($value)) {
            throw new InvalidArgumentException('The value must be an integer or float.');
        }

        return $value * 2;
    }
}
