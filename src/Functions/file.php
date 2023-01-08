<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Functions;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

if (! function_exists('human_readable_filesize')) {
    /**
     * 显示符合人类阅读习惯的文件大小
     *
     * @param  int  $bytes
     * @param  int  $decimals
     * @return string
     */
    function human_readable_filesize(int $bytes, int $decimals = 2): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        $factor = floor(log($bytes, 1024));

        return sprintf("%.{$decimals}f ", $bytes / pow(1024, $factor)) . [
                'B',
                'KB',
                'MB',
                'GB',
                'TB',
                'PB',
                'EB',
                'ZB',
                'YB',
            ][$factor];
    }
}

if (! function_exists('convert_filesize_to_bytes')) {
    /**
     * 文件大小转成 Byte
     *
     * @param  string  $value
     * @return int
     */
    function convert_filesize_to_bytes(string $value): int
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $value = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        $suffix = strtoupper(trim(substr($value, -2)));

        if (intval($suffix) !== 0) {
            $suffix = 'B';
        }

        if (! in_array($suffix, $units)) {
            throw new InvalidArgumentException("invalid unit: $suffix.");
        }

        $number = trim(substr($value, 0, strlen($value) - strlen($suffix)));
        if (! is_numeric($number)) {
            throw new InvalidArgumentException("invalid filesize: $number.");
        }

        return (int)ceil(($number * pow(1024, array_flip($units)[$suffix])));
    }
}

if (! function_exists('fail_if_file_not_exists')) {
    /**
     * 文件不存在则抛出异常
     *
     * @param  string  $file
     * @return void
     */
    function fail_if_file_not_exists(string $file): void
    {
        if (! file_exists($file)) {
            throw new InvalidArgumentException("The file $file not exists.");
        }
    }
}

if (! function_exists('fail_if_file_not_readable')) {
    /**
     * 文件不可读则抛出异常
     *
     * @param  string  $file
     * @return void
     */
    function fail_if_file_not_readable(string $file): void
    {
        if (! is_readable($file)) {
            throw new InvalidArgumentException("The file $file is not readable.");
        }
    }
}

if (! function_exists('fail_if_not_file')) {
    /**
     * 文件不是正常文件则抛出异常
     *
     * @param $file
     * @return void
     */
    function fail_if_not_file($file): void
    {
        fail_if_file_not_exists($file);

        if (! is_file($file)) {
            throw new InvalidArgumentException("The file $file is not a file.");
        }
    }
}

if (! function_exists('fail_if_not_dir')) {
    /**
     * 如果文件不是一个目录则抛出异常
     *
     * @param $file
     * @return void
     */
    function fail_if_not_dir($file): void
    {
        fail_if_file_not_exists($file);
        if (! is_dir($file)) {
            throw new InvalidArgumentException("The file $file is not a dir.");
        }
    }
}

if (! function_exists('file_get_contents_or_fail')) {
    /**
     * 如果文件获取不到内容则抛出异常
     *
     * @param $file
     * @return string
     */
    function file_get_contents_or_fail($file): string
    {
        // 判断是否为本地文件
        if (filter_var($file, FILTER_VALIDATE_URL) === false) {
            fail_if_file_not_exists($file);
            fail_if_file_not_readable($file);
        }

        try {
            $data = file_get_contents($file);
        } catch (Throwable $e) {
            $data = false;
        }

        if ($data === false) {
            throw new RuntimeException("failed to get contents from file $file.");
        }

        return $data;
    }
}

if (! function_exists('scan_dir_or_fail')) {
    /**
     * 列出指定路径中的文件和目录
     *
     * @param  string  $directory
     * @param  bool  $excludeDotDirs
     * @return array
     */
    function scan_dir_or_fail(string $directory, bool $excludeDotDirs = false): array
    {
        fail_if_file_not_exists($directory);
        fail_if_not_dir($directory);

        $scannedDirectory = scandir($directory);
        if ($excludeDotDirs === false) {
            $scannedDirectory = array_diff($scannedDirectory, ['.', '..']);
        }

        return $scannedDirectory;
    }
}

if (! function_exists('join_paths')) {
    /**
     * 合并多个路径
     *
     * @param  string  ...$paths
     * @return string
     */
    function join_paths(string ...$paths): string
    {
        return preg_replace('~[/\\\\]+~', DIRECTORY_SEPARATOR, implode(DIRECTORY_SEPARATOR, $paths));
    }
}

if (! function_exists('file_open_or_fail')) {
    /**
     * 如果文件打开失败则抛出异常
     *
     * @param  string  $file
     * @param  string  $mode
     * @return resource
     */
    function file_open_or_fail(string $file, string $mode)
    {
        fail_if_file_not_exists($file);
        fail_if_file_not_readable($file);

        $fp = fopen($file, $mode);
        if ($fp === false) {
            throw new RuntimeException("failed to open file $file.");
        }

        return $fp;
    }
}

if (! function_exists('csv_to_array')) {
    /**
     * @param  string  $file
     * @param  bool  $parseHeader
     * @param  int  $length
     * @param  string  $delimiter
     * @return array
     * @example
     * Luis,100,100
     * Sebastian,90,90
     * [
     *     [
     *         0 => 'Luis',
     *         1 => 100,
     *         2 => 100
     *     ],
     *     [
     *         0 => 'Sebastian',
     *         1 => 90,
     *         2 => 90
     *     ]
     * ]
     * @example
     * 姓名,语文,数学
     * Luis,100,100
     * Sebastian,90,90
     *
     * [
     *     [
     *         0 => 'Luis',
     *         1 => 100,
     *         2 => 100
     *     ],
     *     [
     *         0 => 'Sebastian',
     *         1 => 90,
     *         2 => 90
     *     ]
     * ]
     *
     */
    function csv_to_array(string $file, bool $parseHeader = true, int $length = 1000, string $delimiter = ','): array
    {
        fail_if_file_not_exists($file);
        fail_if_file_not_readable($file);

        $rows = [];
        $handle = file_open_or_fail($file, 'r');

        $row = fgetcsv($handle, $length, $delimiter);
        if ($parseHeader) {
            $header = $row;
        } else {
            $rows[] = $row;
        }

        try {
            while (($row = fgetcsv($handle, $length, $delimiter)) !== false) {
                if ($parseHeader) {
                    $rows[] = array_combine($header, $row);
                } else {
                    $rows[] = $row;
                }
            }
        } finally {
            fclose($handle);
        }

        ! $parseHeader && array_shift($rows);

        return $rows;
    }
}

if (! function_exists('simply_csv_to_array')) {
    /**
     * @param  string  $file
     * @return array
     */
    function simply_csv_to_array(string $file): array
    {
        $rows = array_map('str_getcsv', file($file));
        $header = array_shift($rows);
        $data = [];
        foreach ($rows as $row) {
            $data[] = array_combine($header, $row);
        }

        return $data;
    }
}

if (! function_exists('path_info')) {
    function path_info(string $path): array
    {
        fail_if_file_not_exists($path);

        $pathInfo = pathinfo($path);
        $pathInfo['extension'] ??= '';

        return $pathInfo;
    }
}

if (! function_exists('make_temp')) {
    function make_temp(string $type): string
    {
        $tempDir = sys_get_temp_dir();
        do {
            $name = join_paths($tempDir, uniqid());
        } while (file_exists($name));

        if ($type === 'file') {
            if (! touch($name)) {
                throw new RuntimeException("failed to touch file: $name");
            }
        } else {
            if (! mkdir($name, 0777, true)) {
                throw new RuntimeException("failed to create directory: $name");
            }
        }

        return $name;
    }
}