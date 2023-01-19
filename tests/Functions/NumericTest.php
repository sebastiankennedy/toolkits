<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Tests\Functions;

use InvalidArgumentException;
use Luyiyuan\Toolkits\Tests\TestCase;

use function Luyiyuan\Toolkits\Functions\compare_float_value;
use function Luyiyuan\Toolkits\Functions\double;

class NumericTest extends TestCase
{
    /**
     * @return void
     */
    public function test_compare_float_value(): void
    {
        $this->assertEquals(true, compare_float_value(1 - 0.8, 0.2));
        $this->assertEquals(true, compare_float_value(1 - 0.8, 0.3, '!=='));
        $this->assertEquals(true, compare_float_value(1 - 0.8, 0.3, '<'));
        $this->assertEquals(false, compare_float_value(1 - 0.8, 0.3, '>'));
        $this->assertEquals(true, compare_float_value(1 - 0.8, 0.2, '<='));
        $this->assertEquals(true, compare_float_value(1 - 0.8, 0.3, '<='));
        $this->assertEquals(false, compare_float_value(1 - 0.8, 0.3, '>='));
    }

    /**
     * @return void
     */
    public function test_double(): void
    {
        $a = 2;
        $b = 4.4;
        $c = '3';

        $this->assertEquals(4, double($a));
        $this->assertEquals(8.8, double($b));
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value must be an integer or float.');
        // @phpstan-ignore-next-line
        double($c);
    }
}
