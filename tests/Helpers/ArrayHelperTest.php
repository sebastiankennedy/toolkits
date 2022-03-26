<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Tests\Helpers;

use Luyiyuan\Toolkits\Helpers\ArrayHelper;
use Luyiyuan\Toolkits\Tests\TestCase;

class ArrayHelperTest extends TestCase
{
    public function test_swap(): void
    {
        $data = [0, 1, 2];
        ArrayHelper::swap($data, 1, 2);
        $this->assertSame($data, [0, 2, 1]);

        $name = ['kennedy', 'sebastian'];
        ArrayHelper::swap($name, 0, 1);
        $this->assertSame($name, ['sebastian', 'kennedy']);
    }
}
