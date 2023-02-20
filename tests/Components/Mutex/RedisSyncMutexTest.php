<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Tests\Components\Mutex;

use Luyiyuan\Toolkits\Components\Mutex\RedisSyncMutex;
use Luyiyuan\Toolkits\Tests\TestCase;
use Predis\Client;
use RuntimeException;

class RedisSyncMutexTest extends TestCase
{
    public function initMutex(): array
    {
        $client = new Client([
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => 6379,
        ]);

        $key = 'resource.domain.';
        $uuid = uniqid();
        $mutex = new RedisSyncMutex(['client' => $client]);

        return [$key, $uuid, $mutex, $client];
    }

    public function testLock(): void
    {
        [$key, $uuid, $mutex, $client] = $this->initMutex();
        $key = $key . time();
        $result = $mutex->lock($key, $uuid, 30);
        $this->assertTrue($result);

        $result = $client->get($key);
        $this->assertSame($uuid, $result);
    }

    public function testUnlock(): void
    {
        [$key, $uuid, $mutex, $client] = $this->initMutex();
        $key = $key . time();
        $result = $mutex->lock($key, $uuid, 60);
        $this->assertTrue($result);

        $result = $mutex->unlock($key, $uuid);
        $this->assertTrue($result);

        $result = $client->get($key);
        $this->assertNull($result);
    }

    public function testDuplicateLock(): void
    {
        [$key, $uuid, $mutex] = $this->initMutex();
        $key = $key . 'A';

        dump('Get Key:' . date('Y-m-d H:i:s'));
        $resultA1 = $mutex->lock($key, $uuid, 30);
        dump('Result A1:' . date('Y-m-d H:i:s') . ',' . $resultA1);

        dump('Get Key:' . date('Y-m-d H:i:s'));
        $resultA2 = $mutex->lock($key, $uuid, 20);
        dump('Result A2:' . date('Y-m-d H:i:s') . ',' . $resultA2);

        dump('Get Key:' . date('Y-m-d H:i:s'));
        $this->expectException(RuntimeException::class);
        $resultA3 = $mutex->lock($key, $uuid, 5);
        dump('Result A3:' . $resultA3 . date('Y-m-d H:i:s'));
    }

    public function testLocked(): void
    {
        [$key, $uuid, $mutex] = $this->initMutex();
        $key = $key . 'B';
        $result = $mutex->lock($key, $uuid, 10);
        $this->assertTrue($result);

        $result = $mutex->locked($key);
        $this->assertTrue($result);
    }

    public function testUnlocked(): void
    {
        [$key, $uuid, $mutex] = $this->initMutex();
        $key = $key . 'C';
        $result = $mutex->lock($key, $uuid, 10);
        $this->assertTrue($result);

        $result = $mutex->locked($key);
        $this->assertTrue($result);

        $result = $mutex->unlock($key, $uuid);
        $this->assertTrue($result);

        $result = $mutex->unlocked($key);
        $this->assertTrue($result);
    }
}