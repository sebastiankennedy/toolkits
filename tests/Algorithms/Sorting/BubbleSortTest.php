<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Tests\Algorithms\Sorting;

use Luyiyuan\Toolkits\Algorithms\Sorting\BubbleSort;
use Luyiyuan\Toolkits\Tests\TestCase;

class BubbleSortTest extends TestCase
{
    public function test_sort(): void
    {
        $array = range(1, 50);
        shuffle($array);
        echo PHP_EOL . implode(',', $array) . PHP_EOL;
        BubbleSort::sort($array);
        $this->assertSame(array_values($array), range(1, 50));
        echo PHP_EOL . implode(',', $array) . PHP_EOL;

        $array = range(1, 50);
        shuffle($array);
        echo PHP_EOL . implode(',', $array) . PHP_EOL;
        BubbleSort::sort($array, 'desc');
        $this->assertSame(array_values($array), array_reverse(range(1, 50)));
        echo PHP_EOL . implode(',', $array) . PHP_EOL;
    }
}
