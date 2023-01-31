<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Algorithms\DynamicProgramming;

/**
 * 零钱兑换
 */
class CoinChange
{
    /**
     * 暴力递归
     *
     * @param  array<integer>  $coins
     * @param  int  $amount
     * @return int
     */
    public static function recursion(array $coins, int $amount): int
    {
        // base case
        if (! $coins) {
            return 0;
        }

        if ($amount === 0) {
            return 0;
        }
        if ($amount < 0) {
            return -1;
        }

        $res = PHP_INT_MAX;
        foreach ($coins as $coin) {
            $subProblem = self::recursion($coins, $amount - $coin);
            if ($subProblem === -1) {
                continue;
            }
            $res = min($res, $subProblem + 1);
        }

        return $res;
    }

    /**
     * 重叠子问题
     *
     * @param  array<int>  $coins
     * @param  int  $amount
     * @return int
     */
    public static function recursionWithHelper(array $coins, int $amount): int
    {
        if (! $coins || $amount <= 0) {
            return 0;
        }

        $meno = [];

        return self::helper($meno, $coins, $amount);
    }

    /**
     * @param  array<int>  $meno
     * @param  array<int>  $coins
     * @param  int  $amount
     * @return int
     */
    public static function helper(array &$meno, array $coins, int $amount): int
    {
        if (isset($meno[$amount])) {
            return $meno[$amount];
        }

        if ($amount < 0) {
            return -1;
        }

        if ($amount === 0) {
            return 0;
        }

        $res = PHP_INT_MAX;
        foreach ($coins as $coin) {
            $subProblem = self::helper($meno, $coins, $amount - $coin);
            if ($subProblem === -1) {
                continue;
            }

            $res = min($res, $subProblem + 1);
        }
        $meno[$amount] = $res;

        return $meno[$amount];
    }

    /**
     * @param  array<int>  $coins
     * @param  int  $amount
     * @return int
     */
    public static function iteration(array $coins, int $amount): int
    {
        // 当目标金额为 i 时，至少需要 $dp[i] 枚硬币凑出
        $dp = array_fill(0, $amount + 1, PHP_INT_MAX);

        // @phpstan-ignore-next-line
        $dp[0] = 0;
        $count = count($dp);
        for ($i = 0; $i < $count; $i++) {
            foreach ($coins as $coin) {
                if ($i - $coin < 0) {
                    continue;
                }
                $dp[$i] = min($dp[$i], 1 + $dp[$i - $coin]);
            }
        }

        return ($dp[$amount] === PHP_INT_MAX) ? -1 : intval($dp[$amount]);
    }
}
