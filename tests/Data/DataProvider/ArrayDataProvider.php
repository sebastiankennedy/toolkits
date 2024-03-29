<?php

namespace Luyiyuan\Toolkits\Tests\Data\DataProvider;

use stdClass;

trait ArrayDataProvider
{
    /**
     * @return array<mixed>
     */
    public function array_index_by_case(): array
    {
        return [
            'index by int' => [
                'data' => [
                    ['id' => 1, 'name' => 'a'],
                    ['id' => 2, 'name' => 'b'],
                    ['id' => 3, 'name' => 'c'],
                ],
                'key' => 'id',
                'expected' => [
                    1 => ['id' => 1, 'name' => 'a'],
                    2 => ['id' => 2, 'name' => 'b'],
                    3 => ['id' => 3, 'name' => 'c'],
                ],
            ],
            'index by string' => [
                'data' => [
                    ['id' => 1, 'name' => 'a'],
                    ['id' => 2, 'name' => 'b'],
                    ['id' => 3, 'name' => 'c'],
                ],
                'key' => 'name',
                'expected' => [
                    'a' => ['id' => 1, 'name' => 'a'],
                    'b' => ['id' => 2, 'name' => 'b'],
                    'c' => ['id' => 3, 'name' => 'c'],
                ],
            ],
            'index by array' => [
                'data' => [
                    ['id' => 1, 'name' => 'a', 'birthday' => ['year' => 2022, 'month' => 4, 'day' => 24]],
                    ['id' => 2, 'name' => 'b', 'birthday' => ['year' => 2022, 'month' => 5, 'day' => 25]],
                    ['id' => 3, 'name' => 'c', 'birthday' => ['year' => 2022, 'month' => 6, 'day' => 26]],
                ],
                'key' => ['birthday', 'day'],
                'expected' => [
                    24 => ['id' => 1, 'name' => 'a', 'birthday' => ['year' => 2022, 'month' => 4, 'day' => 24]],
                    25 => ['id' => 2, 'name' => 'b', 'birthday' => ['year' => 2022, 'month' => 5, 'day' => 25]],
                    26 => ['id' => 3, 'name' => 'c', 'birthday' => ['year' => 2022, 'month' => 6, 'day' => 26]],
                ],
            ],
            'index by callback' => [
                'data' => [
                    ['id' => 1, 'name' => 'a', 'birthday' => ['year' => 2022, 'month' => 4, 'day' => 24]],
                    ['id' => 2, 'name' => 'b', 'birthday' => ['year' => 2022, 'month' => 5, 'day' => 25]],
                    ['id' => 3, 'name' => 'c', 'birthday' => ['year' => 2022, 'month' => 6, 'day' => 26]],
                ],
                'key' => function ($item) {
                    return $item['birthday']['year'] . '年' . $item['birthday']['month'] . '月' . $item['birthday']['day'] . '日';
                },
                'expected' => [
                    '2022年4月24日' => [
                        'id' => 1,
                        'name' => 'a',
                        'birthday' => ['year' => 2022, 'month' => 4, 'day' => 24],
                    ],
                    '2022年5月25日' => [
                        'id' => 2,
                        'name' => 'b',
                        'birthday' => ['year' => 2022, 'month' => 5, 'day' => 25],
                    ],
                    '2022年6月26日' => [
                        'id' => 3,
                        'name' => 'c',
                        'birthday' => ['year' => 2022, 'month' => 6, 'day' => 26],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<mixed>
     */
    public function value_of_key_case(): array
    {
        $object = new stdClass();
        $object->name = 'sebastian';

        return [
            'get object key value' => [
                'item' => $object,
                'key' => 'name',
                'expected' => 'sebastian',
            ],
            'get string key value' => [
                'item' => ['name' => 'sebastian'],
                'key' => 'name',
                'expected' => 'sebastian',
            ],
            'get array key value' => [
                'item' => ['name' => 'sebastian'],
                'key' => ['name'],
                'expected' => 'sebastian',
            ],
            'another get array key value' => [
                'item' => ['name' => ['sex' => 'male'],],
                'key' => ['name', 'sex'],
                'expected' => 'male',
            ],
        ];
    }

    /**
     * @return array<mixed>
     */
    public function array_order_by_case(): array
    {
        return [
            'volume desc' => [
                'data' => [
                    ['volume' => 67, 'edition' => 2],
                    ['volume' => 86, 'edition' => 1],
                    ['volume' => 85, 'edition' => 6],
                    ['volume' => 98, 'edition' => 2],
                    ['volume' => 86, 'edition' => 6],
                    ['volume' => 67, 'edition' => 7],
                ],
                'fieldA' => 'volume',
                'order' => SORT_DESC,
                'expected' => [
                    ['volume' => 98, 'edition' => 2],
                    ['volume' => 86, 'edition' => 1],
                    ['volume' => 86, 'edition' => 6],
                    ['volume' => 85, 'edition' => 6],
                    ['volume' => 67, 'edition' => 2],
                    ['volume' => 67, 'edition' => 7],
                ],
            ],
            'edition asc' => [
                'data' => [
                    ['volume' => 67, 'edition' => 2],
                    ['volume' => 86, 'edition' => 1],
                    ['volume' => 85, 'edition' => 6],
                    ['volume' => 98, 'edition' => 2],
                    ['volume' => 86, 'edition' => 6],
                    ['volume' => 67, 'edition' => 7],
                ],
                'fieldA' => 'edition',
                'order' => SORT_ASC,
                'expected' => [
                    ['volume' => 86, 'edition' => 1],
                    ['volume' => 67, 'edition' => 2],
                    ['volume' => 98, 'edition' => 2],
                    ['volume' => 85, 'edition' => 6],
                    ['volume' => 86, 'edition' => 6],
                    ['volume' => 67, 'edition' => 7],
                ],
            ],
        ];
    }
}
