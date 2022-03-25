<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Algorithms\DynamicProgramming;

class Fibonacci
{
    /**
     * @link 递归：https://laravelacademy.org/post/20952
     * 暴力递归
     *
     * @param  int  $n
     * @return int
     */
    public static function recursion(int $n): int
    {
        // base case，最简单的情况
        if ($n < 2) {
            return $n;
        }

        return self::recursion($n - 1) + self::recursion($n - 2);
    }

    /**
     * 重叠子问题
     *
     * @param  int  $n
     * @return int
     */
    public static function recursionWithHelper(int $n): int
    {
        // 使用哈希表（字典）解决重复计算
        $memo = array_fill(0, $n + 1, 0);

        return self::helper($memo, $n);
    }

    /**
     * @link 递归：https://time.geekbang.org/column/article/41440
     *
     * @param  array<int>  $memo
     * @param  int  $n
     * @return int
     */
    public static function helper(array &$memo, int $n): int
    {
        if ($n < 2) {
            return $n;
        }

        if ($memo[$n] > 0) {
            return $memo[$n];
        }
        $memo[$n] = self::helper($memo, $n - 1) + self::helper($memo, $n - 2);

        return $memo[$n];
    }

    /**
     * 自底向上
     *
     * @param  int  $n
     * @return int
     */
    public static function iteration(int $n): int
    {
        // base case
        if ($n < 2) {
            return $n;
        }

        $dp = array_fill(0, $n + 1, 0);
        // base case
        $dp[1] = $dp[2] = 1;
        for ($i = 3; $i <= $n; $i++) {
            $dp[$i] = $dp[$i - 1] + $dp[$i - 2];
        }

        return $dp[$n];
    }

    /**
     * 状态压缩
     *
     * @param  int  $n
     * @return int
     */
    public static function betterIteration(int $n): int
    {
        if ($n < 2) {
            return $n;
        }

        $prev = $curr = 1;
        for ($i = 3; $i <= $n; $i++) {
            $sum = $prev + $curr;
            $prev = $curr;
            $curr = $sum;
        }

        return $curr;
    }
}
