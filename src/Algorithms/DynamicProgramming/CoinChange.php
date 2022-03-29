<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Algorithms\DynamicProgramming;

class CoinChange
{
    /**
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
}
