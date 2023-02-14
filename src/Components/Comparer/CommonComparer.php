<?php

namespace Luyiyuan\Toolkits\Components\Comparer;

/**
 * 数据解析器
 *
 * @description 一般用于处理二维数组，快速筛选出当前二维数组中，哪些数据是可被创建的、可被更新的、可被删除的。
 *
 */
final class CommonComparer
{
    /**
     * @var array
     */
    private array $creatableAttributes;

    /**
     * @var array
     */
    private array $updatableAttributes;

    /**
     * @var array
     */
    private array $deletableAttributes;

    /**
     * @param  array  $attributes
     * @param  string  $uniqueId
     * @param  string  $deleteId
     */
    public function __construct(array $attributes, string $uniqueId = 'id', string $deleteId = 'deleted_at')
    {
        $this->creatableAttributes = array_filter($attributes, function (array $attribute) use ($uniqueId, $deleteId): bool {
            return ! isset($attribute[$uniqueId]);
        });

        $this->updatableAttributes = array_filter($attributes, function (array $attribute) use ($uniqueId, $deleteId): bool {
            return isset($attribute[$uniqueId]) && (($attribute[$deleteId] ?? false) === false);
        });

        $this->deletableAttributes = array_filter($attributes, function (array $attribute) use ($uniqueId, $deleteId): bool {
            return isset($attribute[$uniqueId]) && (($attribute[$deleteId] ?? false) === true);
        });
    }

    /**
     * @return array
     */
    public function getCreatableAttributes(): array
    {
        return $this->creatableAttributes;
    }

    /**
     * @return array
     */
    public function getUpdatableAttributes(): array
    {
        return $this->updatableAttributes;
    }

    /**
     * @return array
     */
    public function getDeletableAttributes(): array
    {
        return $this->deletableAttributes;
    }
}
