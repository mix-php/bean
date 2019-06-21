<?php

namespace Mix\Bean\Object;

use Mix\Bean\Bean;

/**
 * Interface ObjectInterface
 * @package Mix\Bean\Object
 * @author liu,jian <coder.keda@gmail.com>
 */
interface ObjectInterface
{

    /**
     * 构造
     * BeanObject constructor.
     * @param array $config
     */
    public function __construct($config = []);

    /**
     * 析构
     */
    public function __destruct();

    /**
     * 构造事件
     */
    public function onConstruct();

    /**
     * 初始化事件
     */
    public function onInitialize();

    /**
     * 析构事件
     */
    public function onDestruct();
    
}
