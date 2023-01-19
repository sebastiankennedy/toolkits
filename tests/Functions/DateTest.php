<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Tests\Functions;

use Carbon\Exceptions\InvalidFormatException;
use InvalidArgumentException;
use Luyiyuan\Toolkits\Tests\TestCase;

use function Luyiyuan\Toolkits\Functions\chinese_weekday_to_number;
use function Luyiyuan\Toolkits\Functions\date_of;
use function Luyiyuan\Toolkits\Functions\datetime_of;
use function Luyiyuan\Toolkits\Functions\reformat_date;
use function Luyiyuan\Toolkits\Functions\strtotime_or_fail;
use function Luyiyuan\Toolkits\Functions\to_rfc3339_datetime;
use function Luyiyuan\Toolkits\Functions\weekday_to_chinese;

class DateTest extends TestCase
{
    /**
     * @param  string|null  $name
     * @param  array<mixed>  $data
     * @param  string  $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        date_default_timezone_set("PRC");
    }

    /**
     * @return void
     */
    public function test_datetime_of(): void
    {
        $timestamp = 1643681528;
        $dateTime = "2022-02-01 10:12:08";
        $this->assertSame($dateTime, datetime_of($timestamp));
    }

    /**
     * @return void
     */
    public function test_date_of(): void
    {
        $timestamp = 1643644800;
        $date = "2022-02-01";
        $this->assertSame($date, date_of($timestamp));
    }

    /**
     * @return void
     */
    public function test_strtotime_or_fail(): void
    {
        $dateTime = "2022-02-01 10:12:08";
        $timestamp = 1643681528;
        $this->assertSame($timestamp, strtotime_or_fail($dateTime));

        $dateTime = "SebastianKennedy";
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("failed to convert $dateTime to timestamp.");
        strtotime_or_fail($dateTime);
    }

    /**
     * @return void
     */
    public function test_reformat_date(): void
    {
        $date = '2019-11-21T20:27:14.108227421+08:00';
        $this->assertSame('2019-11-21 20:27:14', reformat_date($date));

        $date = 'SebastianKennedy';
        $this->expectException(InvalidFormatException::class);
        reformat_date($date);
    }

    /**
     * @return void
     */
    public function test_to_rfc3339_datetime(): void
    {
        $dateTime = "2019-11-21 20:27:14";
        $this->assertSame("2019-11-21T20:27:14+08:00", to_rfc3339_datetime($dateTime));
    }

    /**
     * @return void
     */
    public function test_chinese_weekday_to_number(): void
    {
        $this->assertSame(1, chinese_weekday_to_number('星期一'));
        $this->assertSame(2, chinese_weekday_to_number('星期二'));
        $this->assertNull(chinese_weekday_to_number('SebastianKennedy'));
    }

    /**
     * @return void
     */
    public function test_weekday_to_chinese(): void
    {
        $this->assertSame('一', weekday_to_chinese(1));
        $this->assertSame('星期二', weekday_to_chinese(2, true));
        $this->assertNull(weekday_to_chinese(14));
    }
}
