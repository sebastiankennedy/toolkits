<?php

namespace Luyiyuan\Toolkits\Components\Service;

/**
 * 服务策略
 */
interface Strategy
{
    /**
     * @param  array  $data
     * @return array
     */
    public function batchInsert(array $data): array;


    /**
     * @param  array  $data
     * @param  string  $uniqueId
     * @return array
     */
    public function batchUpdate(array $data, string $uniqueId): array;


    /**
     * @param  array  $data
     * @param  string  $uniqueId
     * @return array
     */
    public function batchDelete(array $data, string $uniqueId): array;
}
