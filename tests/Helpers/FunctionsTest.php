<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Tests\Helpers;

use Luyiyuan\Toolkits\Tests\TestCase;

use function Luyiyuan\Toolkits\Helpers\compare_float_value;
use function Luyiyuan\Toolkits\Helpers\human_readable_file_size;
use function PHPUnit\Framework\assertEquals;

class FunctionsTest extends TestCase
{
    public function testHumanReadableFileSize(): void
    {
        $mapping = [
            1022 => '1022 B',
            1023 => '1023 B',
            1024 => '1.00 KB',
            1025 => '1.00 KB',
            1026 => '1.00 KB',
            1027 => '1.00 KB',
            1028 => '1.00 KB',
            1029 => '1.00 KB',
            1030 => '1.01 KB',
            1031 => '1.01 KB',
        ];
        for ($bytes = 1022; $bytes < 1032; $bytes++) {
            self::assertEquals($mapping[$bytes], human_readable_file_size($bytes));
        }

        $bytes = 1032;
        self::assertEquals("1.008 KB", human_readable_file_size($bytes, 3));

        $bytes = 1024 * 1024 * 1024 + 5;
        self::assertEquals("1.0000 GB", human_readable_file_size($bytes, 4));
    }

    public function testCompareFloatValue(): void
    {
        self::assertEquals(true, compare_float_value(1 - 0.8, 0.2));
        self::assertEquals(true, compare_float_value(1 - 0.8, 0.3, '!=='));
        self::assertEquals(true, compare_float_value(1 - 0.8, 0.3, '<'));
        self::assertEquals(false, compare_float_value(1 - 0.8, 0.3, '>'));
        self::assertEquals(true, compare_float_value(1 - 0.8, 0.2, '<='));
        self::assertEquals(true, compare_float_value(1 - 0.8, 0.3, '<='));
        self::assertEquals(false, compare_float_value(1 - 0.8, 0.3, '>='));
    }
}
