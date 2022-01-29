<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Tests\Helpers;

use InvalidArgumentException;
use Luyiyuan\Toolkits\Tests\TestCase;

use function Luyiyuan\Toolkits\Helpers\compare_float_value;
use function Luyiyuan\Toolkits\Helpers\compare_grade;
use function Luyiyuan\Toolkits\Helpers\csv_to_array;
use function Luyiyuan\Toolkits\Helpers\double;
use function Luyiyuan\Toolkits\Helpers\fail_if_file_not_exists;
use function Luyiyuan\Toolkits\Helpers\fail_if_file_not_readable;
use function Luyiyuan\Toolkits\Helpers\fail_if_not_dir;
use function Luyiyuan\Toolkits\Helpers\fail_if_not_file;
use function Luyiyuan\Toolkits\Helpers\file_get_contents_or_fail;
use function Luyiyuan\Toolkits\Helpers\file_open_or_fail;
use function Luyiyuan\Toolkits\Helpers\human_readable_file_size;
use function Luyiyuan\Toolkits\Helpers\rank;
use function Luyiyuan\Toolkits\Helpers\simply_csv_to_array;

/**
 *
 */
class FunctionsTest extends TestCase
{
    public string $file;

    public string $unreadableFile;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->file = __DIR__ . '/./../Data/exam_score_analysis_result.csv';
        $this->unreadableFile = __DIR__ . '/./../Data/unreadable_file.md';
    }

    /**
     * @return void
     */
    public function test_human_readable_file_size(): void
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
            $this->assertEquals($mapping[$bytes], human_readable_file_size($bytes));
        }

        $bytes = 1032;
        $this->assertEquals("1.008 KB", human_readable_file_size($bytes, 3));

        $bytes = 1024 * 1024 * 1024 + 5;
        $this->assertEquals("1.0000 GB", human_readable_file_size($bytes, 4));
    }

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
    public function test_compare_grade(): void
    {
        $a = $b = '一年级';
        $this->assertEquals(0, compare_grade($a, $b));

        $a = '二年级';
        $b = '三年级';
        $this->assertEquals(-1, compare_grade($a, $b));

        $a = '高三';
        $b = '初二';
        $this->assertEquals(1, compare_grade($a, $b));

        $this->assertEquals(-1, compare_grade($a, null));
        $this->assertEquals(-1, compare_grade($a, '大学'));
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
        double($c);
    }

    /**
     * @return void
     */
    public function test_fail_if_file_not_exists(): void
    {
        fail_if_file_not_exists($this->file);

        $this->file .= '_will_fail';
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The file $this->file not exists.");
        fail_if_file_not_exists($this->file);
    }

    /**
     * @return void
     */
    public function test_fail_if_file_not_readable(): void
    {
        chmod($this->file, 0755);
        fail_if_file_not_readable($this->file);

        chmod($this->unreadableFile, 0111);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The file $this->unreadableFile is not readable.");
        fail_if_file_not_readable($this->unreadableFile);
    }

    /**
     * @return void
     */
    public function test_fail_if_not_file(): void
    {
        fail_if_not_file($this->file);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The file " . __DIR__ . " is not a file.");
        fail_if_not_file(__DIR__);
    }

    /**
     * @return void
     */
    public function test_fail_if_not_dir(): void
    {
        fail_if_not_dir(__DIR__);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The file $this->file is not a dir.");
        fail_if_not_dir($this->file);
    }

    /**
     * @return void
     */
    public function test_file_get_contents_or_fail(): void
    {
        $data = file_get_contents_or_fail($this->file);
        self::assertIsString($data);

        chmod($this->unreadableFile, 0111);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The file $this->unreadableFile is not readable.");
        file_get_contents_or_fail($this->unreadableFile);
    }

    /**
     * @return void
     */
    public function test_file_open_or_fail(): void
    {
        file_open_or_fail($this->file, 'r');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The file $this->unreadableFile is not readable.");
        file_open_or_fail($this->unreadableFile, 'r');
    }

    /**
     * @return void
     */
    public function test_csv_to_array(): void
    {
        $arrayA = csv_to_array($this->file);
        $arrayB = csv_to_array($this->file, false);
        $this->assertSameSize($arrayA, $arrayB);

        foreach ($arrayA as $key => $items) {
            $i = 0;
            foreach ($items as $value) {
                $this->assertSame($value, $arrayB[$key][$i]);
                $i++;
            }
        }
    }

    /**
     * @return void
     */
    public function test_simply_csv_to_array(): void
    {
        $arrayA = simply_csv_to_array($this->file);
        $arrayB = csv_to_array($this->file);
        $this->assertSameSize($arrayA, $arrayB);

        foreach ($arrayA as $key => $items) {
            foreach ($items as $field => $value) {
                $this->assertSame($value, $arrayB[$key][$field]);
            }
        }
    }

    /**
     * @return void
     */
    public function test_rank(): void
    {
        $data = simply_csv_to_array($this->file);
        $temp = [];
        foreach ($data as $value) {
            $temp[$value['考试科目名称']][] = [
                'subject_name' => $value['考试科目名称'],
                'student_name' => $value['姓名'],
                'score' => $value['原始分数'],
                'rank' => null,
                'rank_base' => null,
                'rank_percent' => null,
            ];
        }
        dump($temp['总分']);

        foreach ($temp as $key => $scores) {
            $temp[$key] = rank($scores, 'score', 2);
        }
    }
}
