<?php

namespace Mix\Bean\Object;

use Mix\Bean\Bean;
use Mix\Bean\Exception\InstantiationException;
use Mix\Bean\Injector;

/**
 * Class AbstractObject
 * @package Mix\Bean\Object
 * @author liu,jian <coder.keda@gmail.com>
 */
abstract class AbstractObject implements ObjectInterface
{

    /**
     * 构造
     * AbstractObject constructor.
     * @param array $config
     * @throws \ReflectionException
     */
    public function __construct($config = [])
    {
        // 执行构造事件
        $this->onConstruct();
        // 注入属性
        Injector::inject($this, $config);
        // 执行初始化事件
        $this->onInitialize();
    }

    /**
     * 析构
     */
    public function __destruct()
    {
        $this->onDestruct();
    }

    /**
     * 构造事件
     */
    public function onConstruct()
    {
    }

    /**
     * 初始化事件
     */
    public function onInitialize()
    {
    }

    /**
     * 析构事件
     */
    public function onDestruct()
    {
    }

    /**
     * 使用依赖创建实例
     * @param Bean $bean
     * @return ObjectInterface
     */
    public static function newInstance(Bean $bean)
    {
        $current = get_called_class();
        $class   = $bean->getClass();
        if ($class != $current) {
            throw new InstantiationException("Bean class is not equal to the current class, Current class: {$current}, Bean class: {$class}");
        }
        return $bean->newInstance();
    }

    /**
     * 通过对象创建实例
     * 为了实现类型的代码补全
     * @param $object
     * @return $this
     */
    public static function make($object)
    {
        $currentClass = get_called_class();
        $class        = get_class($object);
        if ($currentClass != $class) {
            throw new InstantiationException("Type mismatch: Current class: {$currentClass}, Parameter class: {$class}");
        }
        return $object;
    }

}
