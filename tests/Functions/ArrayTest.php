<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Tests\Functions;

use Luyiyuan\Toolkits\Tests\Data\DataProvider\ArrayDataProvider;
use Luyiyuan\Toolkits\Tests\TestCase;

use function Luyiyuan\Toolkits\Functions\array_index_by;
use function Luyiyuan\Toolkits\Functions\array_order_by;
use function Luyiyuan\Toolkits\Functions\compare_grade;
use function Luyiyuan\Toolkits\Functions\is_difference_of_set;
use function Luyiyuan\Toolkits\Functions\is_intersection_of_set;
use function Luyiyuan\Toolkits\Functions\is_subset_of_set;
use function Luyiyuan\Toolkits\Functions\rank;
use function Luyiyuan\Toolkits\Functions\simply_csv_to_array;
use function Luyiyuan\Toolkits\Functions\value_of_key;

class ArrayTest extends TestCase
{
    use ArrayDataProvider;

    public string $file;

    /**
     * @param  string|null  $name
     * @param  array  $data
     * @param  string  $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->file = __DIR__ . '/./../Data/Csv/scores.csv';
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

        foreach ($temp as $key => $scores) {
            $temp[$key] = rank($scores);
        }
        $this->assertEquals(36, $temp['语文'][0]['rank']);
        $this->assertEquals(36, $temp['总分'][35]['rank']);
    }


    /**
     * @dataProvider array_index_by_case
     * @param  array  $data
     * @param  array|string|int|callable  $key
     * @param  array  $expected
     * @return void
     */
    public function test_array_index_by(array $data, $key, array $expected): void
    {
        $this->assertEquals($expected, array_index_by($data, $key));
    }

    /**
     * @dataProvider value_of_key_case
     * @param mixed $item
     * @param mixed $key
     * @param mixed $expected
     * @return void
     */
    public function test_value_of_key($item, $key, $expected): void
    {
        $this->assertEquals($expected, value_of_key($item, $key));
    }

    /**
     * @dataProvider array_order_by_case
     * @param  array  $data
     * @param  string  $fieldA
     * @param  int  $order
     * @param  array  $expected
     * @return void
     */
    public function test_array_order_by(array $data, string $fieldA, int $order, array $expected): void
    {
        $this->assertEquals($expected, array_order_by($data, $fieldA, $order));
    }

    /**
     * @return void
     */
    public function test_is_intersection_of_set(): void
    {
        $a = ['a', 'b', 'c'];
        $b = ['a', 'b', 'c', 'd', 'e', 'f'];
        $c = ['d', 'e', 'f'];
        $this->assertSame(true, is_intersection_of_set($a, $b));
        $this->assertSame(true, is_intersection_of_set($b, $a));
        $this->assertSame(false, is_intersection_of_set($a, $c));
    }

    /**
     * @return void
     */
    public function test_is_subset_of_set(): void
    {
        $a = ['a', 'b', 'c'];
        $b = ['a', 'b', 'c', 'd', 'e', 'f'];
        $this->assertSame(true, is_subset_of_set($a, $b));
        $this->assertSame(false, is_subset_of_set($b, $a));
    }

    /**
     * @return void
     */
    public function test_is_difference_of_set(): void
    {
        $a = ['a', 'b', 'c'];
        $b = ['a', 'b', 'c', 'd', 'e', 'f'];
        $c = ['d', 'e', 'f'];
        $this->assertSame(false, is_difference_of_set($a, $b));
        $this->assertSame(true, is_difference_of_set($b, $a));
        $this->assertSame(true, is_difference_of_set($a, $c));
    }
}
