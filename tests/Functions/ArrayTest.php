<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Tests\Functions;

use Luyiyuan\Toolkits\Tests\TestCase;

use function Luyiyuan\Toolkits\Functions\compare_grade;
use function Luyiyuan\Toolkits\Functions\rank;
use function Luyiyuan\Toolkits\Functions\simply_csv_to_array;

class ArrayTest extends TestCase
{
    public string $file;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->file = __DIR__ . '/./../Data/exam_score_analysis_result.csv';
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
}
