<?php

namespace Mix\Bean\Object;

use Mix\Bean\Bean;
use Mix\Bean\Exception\InstantiationException;
use Mix\Bean\Injector;

/**
 * Trait ObjectTrait
 * @package Mix\Bean\Object
 * @author liu,jian <coder.keda@gmail.com>
 */
trait ObjectTrait
{

    /**
     * 构造
     * ObjectTrait constructor.
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

}
