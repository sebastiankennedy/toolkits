<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Functions;

use Carbon\Carbon;
use InvalidArgumentException;

if (! function_exists('datetime_of')) {
    /**
     * @param  int|null  $timestamp
     * @return string
     */
    function datetime_of(?int $timestamp = null): string
    {
        return date('Y-m-d H:i:s', $timestamp ?? time());
    }
}

if (! function_exists('date_of')) {
    /**
     * @param  int|null  $timestamp
     * @return string
     */
    function date_of(?int $timestamp = null): string
    {
        return date('Y-m-d', $timestamp ?? time());
    }
}

if (! function_exists('strtotime_or_fail')) {
    /**
     * @param  string  $date
     * @return int
     */
    function strtotime_or_fail(string $date): int
    {
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            throw new InvalidArgumentException("failed to convert $date to timestamp.");
        }

        return $timestamp;
    }
}

if (! function_exists('reformat_date')) {
    /**
     * @param  string  $date
     * @return string
     * @example
     * 2019-11-21T20:27:14.108227421+08:00
     *
     */
    function reformat_date(string $date): string
    {
        $pos = strpos($date, '.');
        if ($pos !== false) {
            $plusPos = strpos($date, '+');
            assert($plusPos !== false);
            $date = substr($date, 0, $pos) . substr($date, $plusPos);
        }
        $carbon = Carbon::make($date);
        assert($carbon !== null);

        return $carbon->format('Y-m-d H:i:s');
    }
}

if (! function_exists('to_rfc3339_datetime')) {
    /**
     * RFC - Request For Comment
     * RFC 3339 是 DateTime 格式文档
     *
     * @param  string  $datetime
     * @return string
     */
    function to_rfc3339_datetime(string $datetime): string
    {
        $pos = strpos($datetime, 'T');
        if ($pos !== false) {
            return $datetime;
        }
        $timestamp = strtotime($datetime);
        if ($timestamp === false) {
            throw new InvalidArgumentException("failed to convert $datetime to rfc3339 datetime.");
        }

        return date(DATE_RFC3339, $timestamp);
    }
}

if (! function_exists('chinese_weekday_to_number')) {
    /**
     * @param  string  $weekday
     * @return int|null
     */
    function chinese_weekday_to_number(string $weekday): ?int
    {
        $map = ['一' => 1, '二' => 2, '三' => 3, '四' => 4, '五' => 5, '六' => 6, '日' => 7, '天' => 7];
        $otherMap = ['星期一' => 1, '星期二' => 2, '星期三' => 3, '星期四' => 4, '星期五' => 5, '星期六' => 6, '星期日' => 7, '星期天' => 7];

        return $map[$weekday] ?? ($otherMap[$weekday] ?? null);
    }
}

if (! function_exists('weekday_to_chinese')) {
    /**
     * @param  int  $weekday
     * @param  bool  $fullName
     * @return string|null
     */
    function weekday_to_chinese(int $weekday, bool $fullName = false): ?string
    {
        $map = [null, '一', '二', '三', '四', '五', '六', '日'];
        $otherMap = [null, '星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期日'];

        return $fullName
            ? ($otherMap[$weekday] ?? null)
            : ($map[$weekday] ?? null);
    }
}
