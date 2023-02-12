<?php

namespace Luyiyuan\Toolkits\Components\Service;

use Luyiyuan\Toolkits\DesignPatterns\Singleton\ConceptualSchema\Singleton;

/**
 * 服务
 */
abstract class Service extends Singleton
{
    /**
     * @var mixed
     */
    private Strategy $strategy;

    /**
     * @param  Strategy  $strategy
     * @return void
     */
    public function setStrategy(Strategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @param  array  $data
     * @return array
     */
    public function batchInsertByStrategy(array $data): array
    {
        return $this->strategy->batchInsert($data);
    }

    /**
     * @param  array  $data
     * @param  string  $uniqueId
     * @return array
     */
    public function batchUpdateByStrategy(array $data, string $uniqueId): array
    {
        return $this->strategy->batchUpdate($data, $uniqueId);
    }


    /**
     * @param  array  $data
     * @param  string  $uniqueId
     * @return array
     */
    public function batchDeleteByStrategy(array $data, string $uniqueId): array
    {
        return $this->strategy->batchDelete($data, $uniqueId);
    }
}
