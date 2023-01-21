<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Tests\DesignPatterns\Singleton;

use LogicException;
use Luyiyuan\Toolkits\DesignPatterns\Singleton\ConceptualSchema\Singleton;
use Luyiyuan\Toolkits\Tests\TestCase;

/**
 * Class SingletonTest - 单例模式单元测试
 * @package Luyiyuan\Toolkits\Tests\DesignPatterns\Singleton
 */
class SingletonTest extends TestCase
{
    public function test_ensure_a_class_has_only_one_instance(): void
    {
        $singletonA = Singleton::getInstance();
        $singletonB = Singleton::getInstance();
        $this->assertEquals($singletonA, $singletonB);
    }

    public function test_singleton_cannot_be_json_serialize(): void
    {
        $singleton = Singleton::getInstance();
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("cannot json serialize singleton.");
        json_encode($singleton);
    }

    public function test_singleton_cannot_be_cloneable(): void
    {
        $singleton = Singleton::getInstance();
        $this->assertInstanceOf(Singleton::class, $singleton);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("cannot clone singleton.");
        $clone = clone $singleton;
        $this->assertNotEquals($singleton, $clone);
    }

    public function test_singleton_cannot_be_serializable(): void
    {
        $singleton = Singleton::getInstance();
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("cannot serialize singleton.");
        serialize($singleton);
    }
}
