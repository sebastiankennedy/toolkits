<?php

namespace Luyiyuan\Toolkits\Components\Mutex;

use InvalidArgumentException;
use Luyiyuan\Toolkits\Components\Configuration\Configurable;
use Predis\Client;
use RuntimeException;

/**
 * Redis 同步锁
 *
 * @link https://xiaomi-info.github.io/2019/12/17/redis-distributed-lock/
 */
class RedisSyncMutex implements SyncMutex
{
    use Configurable;

    /**
     * @var int - 单位：秒
     */
    public int $ttl = 900;

    /**
     * @var Client
     */
    public Client $client;

    /**
     * @param  string  $key
     * @param  string  $uuid
     * @param  int|null  $ttl
     * @param  int|null  $maxWait
     * @param  int|null  $sleepMs
     * @return void
     *
     * $maxWait 最大为 2 倍的 $ttl ，过长地等待只会导致更多问题，及时暴露异常。
     */
    public function lock(string $key, string $uuid, ?int $ttl = null, ?int $maxWait = null, ?int $sleepMs = null): bool
    {
        if (empty($key) || empty($uuid)) {
            throw new InvalidArgumentException('key or uuid must not be empty');
        }

        /*
         * 超时解锁导致并发
         * 如果线程 A 成功获取锁并设置过期时间 30 秒，但线程 A 执行时间超过了 30 秒，锁过期自动释放，此时线程 B 获取到了锁，线程 A 和线程 B 并发执行。
         * 将过期时间设置足够长，确保代码逻辑在锁释放之前能够执行完成。
         */
        $ttl = $ttl ?: $this->ttl;
        $maxWait = $maxWait ?: 2 * $ttl;
        $sleepMs ??= 200;
        $sleepMs *= 1000;
        $startAt = time();

        do {
            /*
             * 锁原子性
             * 由于 setnx 和 expire 命令非原子性，在执行 setnx 命令成功后，因为各种网络问题导致没有执行 expire 命令成功
             * 锁就会因为没有设置超时时间变成死锁，因此需要使用 set 命令或者 lua 脚本。(Redis 2.8 以后支持)
             *
             * 锁误解除
             * 如果线程 A 成功获取了锁，并设置了过期时间 30 秒，但线程 A 执行时间超过了 30 秒，锁过期自动释放，此时线程 B 获取到了锁；
             * 随后线程 A 执行完成，线程 A 使用 DEL 命令来释放锁，但此时线程 B 加的锁还没有执行完成，线程 A 实际释放的线程 B 加的锁。
             * 所以需要存入一个 uuid 来判断当前 uuid 是否属于当前线程。
             */
            $result = $this->client->set($key, $uuid, 'EX', $ttl, 'NX');
            if ($result) {
                break;
            }

            $waited = time() - $startAt;
            usleep($sleepMs);
        } while ($waited < $maxWait);

        if ($result) {
            return true;
        }

        throw new RuntimeException("failed to lock key $key in $maxWait seconds");
    }

    /**
     * @param  string  $key
     * @param  string  $uuid
     * @return void
     */
    public function unlock(string $key, string $uuid): bool
    {
        if (empty($key) || empty($uuid)) {
            throw new InvalidArgumentException('key or uuid must not be empty');
        }

        /*
         * 锁误解除
         * $this->client->get($key);
         * if ($this->client->get($key) === $uuid) {
         *     $this->client->del($key);
         * }
         *
         * 这样判断不够严谨，不是原子性的操作。
         * 比如线程 A 加锁，代码执行后进行解锁操作，在执行 del 锁之前锁过期，这时候线程 B 加锁成功，接着线程 A 执行 del 锁就会将线程 B 的锁删除，没有保证同一性。
         * 需要使用 Lua 脚本来保证执行操作的原子性
         */
        $luaScript = <<<SCRIPT
if redis.call("get",KEYS[1]) == ARGV[1]
then
    return redis.call("del",KEYS[1])
else
    return 0
end
SCRIPT;
        $result = $this->client->eval($luaScript, 1, $key, $uuid);
        if ($result) {
            return true;
        }

        return false;
    }

    /**
     * @param  string  $key
     * @return bool
     */
    public function locked(string $key): bool
    {
        return $this->client->get($key) !== null;
    }

    /**
     * @param  string  $key
     * @return bool
     */
    public function unlocked(string $key): bool
    {
        return ! $this->locked($key);
    }
}
