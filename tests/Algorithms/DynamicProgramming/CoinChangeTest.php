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
        echo $res . PHP_EOL;
        $this->markTestIncomplete();
    }
}