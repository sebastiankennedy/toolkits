<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Tests\Functions;

use InvalidArgumentException;
use Luyiyuan\Toolkits\Tests\TestCase;

use function Luyiyuan\Toolkits\Functions\convert_filesize_to_bytes;
use function Luyiyuan\Toolkits\Functions\csv_to_array;
use function Luyiyuan\Toolkits\Functions\human_readable_filesize;
use function Luyiyuan\Toolkits\Functions\join_paths;
use function Luyiyuan\Toolkits\Functions\path_info;
use function Luyiyuan\Toolkits\Functions\scan_dir_or_fail;
use function Luyiyuan\Toolkits\Functions\simply_csv_to_array;
use function Luyiyuan\Toolkits\Functions\fail_if_file_not_exists;
use function Luyiyuan\Toolkits\Functions\fail_if_file_not_readable;
use function Luyiyuan\Toolkits\Functions\fail_if_not_dir;
use function Luyiyuan\Toolkits\Functions\fail_if_not_file;
use function Luyiyuan\Toolkits\Functions\file_get_contents_or_fail;
use function Luyiyuan\Toolkits\Functions\file_open_or_fail;

/**
 *
 */
class FileTest extends TestCase
{
    /**
     * @var string
     */
    public string $file;

    /**
     * @var string
     */
    public string $unreadableFile;

    /**
     * @param  string|null  $name
     * @param  array<mixed>  $data
     * @param  string  $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->file = __DIR__ . '/./../Data/Csv/scores.csv';
        $this->unreadableFile = __DIR__ . '/./../Data/Csv/unreadable_file.csv';
    }

    /**
     * @return void
     */
    public function test_human_readable_filesize(): void
    {
        $testCases = [
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
            $this->assertEquals($testCases[$bytes], human_readable_filesize($bytes));
        }

        $bytes = 1032;
        $this->assertEquals("1.008 KB", human_readable_filesize($bytes, 3));

        $bytes = 1024 * 1024 * 1024 + 5;
        $this->assertEquals("1.0000 GB", human_readable_filesize($bytes, 4));
    }

    public function test_convert_filesize_to_bytes(): void
    {
        $testCases = [
            '13b' => 13,
            '13B' => 13,
            '13KB' => 13312,
            '10.5KB' => 10752,
            '5Gb' => 5368709120,
            '533Mb' => 558891008,
            '10.64GB' => 11424613008,
        ];
        foreach ($testCases as $key => $value) {
            $this->assertEquals(convert_filesize_to_bytes($key), $value);
        }

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid filesize: 1as.11');
        convert_filesize_to_bytes('1as.11GB');

        $this->expectExceptionMessage('invalid filesize: CS');
        convert_filesize_to_bytes('123 ABCS');
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
    public function test_scan_dir_or_fail(): void
    {
        $dir = __DIR__;
        $scannedDirectory = scan_dir_or_fail($dir);
        $this->assertSame(count($scannedDirectory), 4);

        $scannedDirectory = scan_dir_or_fail($dir, true);
        $this->assertSame(count($scannedDirectory), 6);
    }

    /**
     * @return void
     */
    public function test_join_paths(): void
    {
        $dir = __DIR__;
        $name = 'FileTest.php';
        $path = join_paths($dir, $name);
        $this->assertSame($path, __FILE__);
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

    public function test_path_info(): void
    {
        $pathInfo = path_info($this->file);
        $this->assertSame(count($pathInfo), 4);

        $envFile = __DIR__ . '/./../Data/Files/.env';
        $pathInfo = path_info($envFile);
        $this->assertSame($pathInfo['basename'], '.env');

        $envFile = __DIR__ . '/./../Data/Files/.env.example';
        $pathInfo = path_info($envFile);
        $this->assertSame($pathInfo['filename'], '.env');

        $dir = __DIR__ . '/./../Data/Csv';
        $pathInfo = path_info($dir);
        $this->assertSame(count($pathInfo), 4);

        $chineseFile = __DIR__ . '/./../Data/Csv/中文2022.csv';
        $pathInfo = path_info($chineseFile);
        $this->assertSame($pathInfo['filename'], '中文2022');
    }
}
