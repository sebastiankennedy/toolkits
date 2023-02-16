<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Components\Comparer;

/**
 * 解析器
 */
interface Comparer
{
    /**
     * @return array
     */
    public function getCreatableAttributes(): array;

    /**
     * @return array
     */
    public function getUpdatableAttributes(): array;

    /**
     * @return array
     */
    public function getDeletableAttributes(): array;

    /**
     * @param  array<array>  $attributes
     * @return array
     */
    public function resolveCreatableAttributes(array $attributes): array;

    /**
     * @param  array<array>  $attributes
     * @return array
     */
    public function resolveUpdatableAttributes(array $attributes): array;

    /**
     * @param  array<array>  $attributes
     * @return array
     */
    public function resolveDeletableAttributes(array $attributes): array;
}
