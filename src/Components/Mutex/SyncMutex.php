<?php

namespace Luyiyuan\Toolkits\Components\Mutex;

/**
 * 同步锁
 */
interface SyncMutex
{
    /**
     * 上锁
     *
     * @param  string  $key
     * @param  string  $uuid
     * @param  int|null  $ttl
     * @param  int|null  $maxWait
     * @param  int|null  $sleepMs
     * @return void
     */
    public function lock(string $key, string $uuid, ?int $ttl = null, ?int $maxWait = null, ?int $sleepMs = null): void;

    /**
     * 解锁
     *
     * @param  string  $key
     * @param  string  $uuid
     * @return void
     */
    public function unlock(string $key, string $uuid): void;

    /**
     * 已完成上锁
     *
     * @param  string  $key
     * @return bool
     */
    public function locked(string $key): bool;

    /**
     * 已完成解锁
     *
     * @param  string  $key
     * @return bool
     */
    public function unlocked(string $key): bool;
}
