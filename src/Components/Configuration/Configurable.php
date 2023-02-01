<?php

namespace Luyiyuan\Toolkits\Components\Configuration;

use InvalidArgumentException;

trait Configurable
{
    public function __construct($config = [])
    {
        foreach ($config as $name => $value) {
            $this->$name = $value;
        }

        $this->init();
    }

    public function init()
    {
    }

    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidArgumentException('Setting read-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new InvalidArgumentException('Setting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (method_exists($this, 'set' . $name)) {
            throw new InvalidArgumentException('Getting write-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new InvalidArgumentException('Getting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    public function __isset($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        } else {
            return false;
        }
    }

    public function __unset($name)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter(null);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidArgumentException('Unsetting read-only property: ' . get_class($this) . '::' . $name);
        }
    }

    public function __call($name, $params)
    {
        throw new InvalidArgumentException('Calling unknown method: ' . get_class($this) . "::$name()");
    }
}
