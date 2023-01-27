<?php

namespace Luyiyuan\Toolkits\Components\Comparer;

Final class AttributesComparer
{
    private array $creatableData;

    private array $updatableData;

    private array $deletableData;

    public function __construct(array $data, string $uniqueId = 'id', string $deleteId = 'deleted_at')
    {
        $this->creatableData = array_filter($data, function (array $attribute) use ($uniqueId, $deleteId): bool {
            return ! isset($attribute[$uniqueId]);
        });

        $this->updatableData = array_filter($data, function (array $attribute) use ($uniqueId, $deleteId): bool {
            return isset($attribute[$uniqueId]) && (($attribute[$deleteId] ?? false) === false);
        });

        $this->deletableData = array_filter($data, function (array $attribute) use ($uniqueId, $deleteId): bool {
            return isset($attribute[$uniqueId]) && (($attribute[$deleteId] ?? false) === true);
        });
    }

    public function getCreatableData(): array
    {
        return $this->creatableData;
    }

    public function getUpdatableData(): array
    {
        return $this->updatableData;
    }

    public function getDeletableData(): array
    {
        return $this->deletableData;
    }
}
