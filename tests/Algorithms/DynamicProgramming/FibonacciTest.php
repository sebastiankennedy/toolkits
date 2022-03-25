<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Tests\Algorithms\DynamicProgramming;

use Luyiyuan\Toolkits\Algorithms\DynamicProgramming\Fibonacci;
use Luyiyuan\Toolkits\Tests\TestCase;

class FibonacciTest extends TestCase
{
    public function test_recursion(): void
    {
        $this->assertEquals(0, Fibonacci::recursion(0));
        $this->assertEquals(1, Fibonacci::recursion(1));
        $this->assertEquals(1, Fibonacci::recursion(2));
        $this->assertEquals(2, Fibonacci::recursion(3));
        $this->assertEquals(55, Fibonacci::recursion(10));
        $this->assertEquals(89, Fibonacci::recursion(11));
        $this->assertEquals(10946, Fibonacci::recursion(21));
        $this->assertEquals(17711, Fibonacci::recursion(22));
    }

    public function test_recursion_with_helper(): void
    {
        $this->assertEquals(0, Fibonacci::recursionWithHelper(0));
        $this->assertEquals(1, Fibonacci::recursionWithHelper(1));
        $this->assertEquals(1, Fibonacci::recursionWithHelper(2));
        $this->assertEquals(2, Fibonacci::recursionWithHelper(3));
        $this->assertEquals(55, Fibonacci::recursionWithHelper(10));
        $this->assertEquals(89, Fibonacci::recursionWithHelper(11));
        $this->assertEquals(10946, Fibonacci::recursionWithHelper(21));
        $this->assertEquals(17711, Fibonacci::recursionWithHelper(22));
    }

    public function test_iteration(): void
    {
        $this->assertEquals(0, Fibonacci::iteration(0));
        $this->assertEquals(1, Fibonacci::iteration(1));
        $this->assertEquals(1, Fibonacci::iteration(2));
        $this->assertEquals(2, Fibonacci::iteration(3));
        $this->assertEquals(55, Fibonacci::iteration(10));
        $this->assertEquals(89, Fibonacci::iteration(11));
        $this->assertEquals(10946, Fibonacci::iteration(21));
        $this->assertEquals(17711, Fibonacci::iteration(22));
    }

    public function test_better_iteration(): void
    {
        $this->assertEquals(0, Fibonacci::betterIteration(0));
        $this->assertEquals(1, Fibonacci::betterIteration(1));
        $this->assertEquals(1, Fibonacci::betterIteration(2));
        $this->assertEquals(2, Fibonacci::betterIteration(3));
        $this->assertEquals(55, Fibonacci::betterIteration(10));
        $this->assertEquals(89, Fibonacci::betterIteration(11));
        $this->assertEquals(10946, Fibonacci::betterIteration(21));
        $this->assertEquals(17711, Fibonacci::betterIteration(22));
    }
}
