<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Tests\Algorithms\DynamicProgramming;

use Luyiyuan\Toolkits\Algorithms\DynamicProgramming\CoinChange;
use Luyiyuan\Toolkits\Tests\TestCase;

class CoinChangeTest extends TestCase
{
    public function test_recursion(): void
    {
        $res = CoinChange::recursion([1, 2, 5], 2);
        $this->assertEquals(1, $res);

        $res = CoinChange::recursion([1, 2, 5], 11);
        $this->assertEquals(3, $res);

        $res = CoinChange::recursion([1, 2, 5], 26);
        $this->assertEquals(6, $res);
    }

    public function test_recursion_with_helper(): void
    {
        $res = CoinChange::recursionWithHelper([1, 2, 5], 4);
        $this->assertEquals(2, $res);

        $res = CoinChange::recursionWithHelper([1, 2, 5], 11);
        $this->assertEquals(3, $res);

        $res = CoinChange::recursionWithHelper([1, 2, 5], 100);
        $this->assertEquals(20, $res);
    }

    public function test_iteration(): void
    {
        $res = CoinChange::iteration([1, 2, 5], 4);
        $this->assertEquals(2, $res);

        $res = CoinChange::iteration([1, 2, 5], 11);
        $this->assertEquals(3, $res);

        $res = CoinChange::iteration([1, 2, 5], 100);
        $this->assertEquals(20, $res);
    }
}
