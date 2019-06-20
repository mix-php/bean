<?php

namespace Mix\Bean;

use Mix\Core\Application;

/**
 * Class Bean
 * @package Mix\Bean
 * @author liu,jian <coder.keda@gmail.com>
 */
class Bean
{

    /**
     * @var Application
     */
    public $app;

    /**
     * @var array
     */
    public $config;

    /**
     * Bean constructor.
     */
    public function __construct($app, $config)
    {
        // 导入属性
        $this->app    = $app;
        $this->config = $config;
    }

    /**
     * 获取类名
     * @return string
     */
    public function getClass()
    {
        $config = $this->config;
        return $config['class'];
    }

    /**
     * 创建实例
     * @return mixed
     */
    public function newInstance()
    {
        $config     = $this->config;
        $class      = $config['class'];
        $properties = $config['properties'] ?? [];
        $properties = Injector::build($this->app, $properties);
        return new $class($properties);
    }

}
