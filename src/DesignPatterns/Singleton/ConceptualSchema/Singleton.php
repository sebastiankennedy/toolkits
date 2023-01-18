<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\DesignPatterns\Singleton\ConceptualSchema;

use JsonSerializable;
use LogicException;

/**
 * Class Singleton
 * @package Luyiyuan\Toolkits\DesignPatterns\Singleton
 *
 * 如果程序中的某个类对于所有客户端只有一个可用的实例，可以使用单例模式，例如日志、配置。
 * 如果你需要更加严格地控制全局变量，可以使用单例模式。
 */
class Singleton implements JsonSerializable
{
    /**
     * 在类中添加一个私有静态成员变量用于保存单例实例。
     *
     * @var Singleton|null
     */
    private static ?Singleton $instance = null;

    /**
     * @var bool
     */
    private static bool $again = false;

    /**
     * 将类的构造函数设为私有。
     * 类的静态方法仍能调用构造函数，但是其他对象不能调用。
     */
    private function __construct()
    {
        if (static::$again) {
            throw new LogicException("cannot instantiate singleton again.");
        } else {
            static::$again = true;
        }
    }

    /**
     * 单例模式为了保证一个类只有一个实例，无法被克隆。
     */
    public function __clone()
    {
        throw new LogicException("cannot clone singleton.");
    }

    /**
     * 单例模式为了保证一个类只有一个实例，无法被序列化。
     */
    public function __serialize(): void
    {
        throw new LogicException("cannot serialize singleton.");
    }

    /**
     * 单例模式为了保证一个类只有一个实例，无法被 JSON 编码。
     */
    public function jsonSerialize(): void
    {
        throw new LogicException("cannot json serialize singleton.");
    }

    /**
     * 单例模式在静态方法中实现"延迟初始化"。
     * 此方法会在首次被调用时创建一个新的实例，并将其存储在静态成员变量中。
     * 此后该方法每次被调用时都返回该实例。
     * 单例模式禁止通过除特殊构建方法以外的任何方式来创建自身类的对象。
     *
     * @return Singleton
     */
    public static function getInstance(): Singleton
    {
        if (null === self::$instance) {
            // 单例模式在多线程环境下需要进行特殊处理，避免多个线程多次创建单例对象。
            self::$instance = new self();
        }

        return self::$instance;
    }
}
