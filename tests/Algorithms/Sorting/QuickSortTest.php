<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Tests\Algorithms\Sorting;

use Luyiyuan\Toolkits\Algorithms\Sorting\QuickSort;
use Luyiyuan\Toolkits\Tests\TestCase;

use function Luyiyuan\Toolkits\Functions\csv_to_array;

class QuickSortTest extends TestCase
{
    public array $array;

    public array $multiArray;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->array = range(1, 20);
        shuffle($this->array);
    }

    public function test_sort(): void
    {
        QuickSort::sort($this->array);
        $this->assertSame(array_values($this->array), range(1, 20));
        echo PHP_EOL . implode(',', $this->array) . PHP_EOL;
    }

    public function test_sort_by(): void
    {
        $this->multiArray = csv_to_array(__DIR__ . '/./../../Data/exam_scores.csv');
        shuffle($this->multiArray);

        $temp = [];
        foreach ($this->multiArray as $value) {
            $temp[$value['考试科目名称']][] = [
                'subject_name' => $value['考试科目名称'],
                'student_name' => $value['姓名'],
                'score' => $value['原始分数'],
                'rank' => null,
                'rank_base' => null,
                'rank_percent' => null,
            ];
        }

        foreach ($temp as $key => $items) {
            QuickSort::sortBy('score', $items);

            echo str_repeat('=', 14) . $key . str_repeat('=', 14) . PHP_EOL;
            foreach ($items as $item) {
                echo sprintf("学生%s在科目%s得分为%s", $item['student_name'], $item['subject_name'], $item['score']) . PHP_EOL;
            }
        }

        $this->markTestIncomplete();
    }
}
