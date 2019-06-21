<?php

namespace Mix\Bean;

use Psr\Container\ContainerInterface;

/**
 * Class Bean
 * @package Mix\Bean
 * @author liu,jian <coder.keda@gmail.com>
 */
class Bean
{

    /**
     * @var Beans
     */
    public $beans;

    /**
     * @var ContainerInterface
     */
    public $container;

    /**
     * @var array
     */
    public $config;

    /**
     * Beans constructor.
     * @param $config
     */
    public function __construct($config)
    {
        // 导入属性
        $this->beans     = $config['beans'];
        $this->container = $config['container'];
        $this->config    = $config['config'];
    }

    /**
     * 获取Bean名称
     * @param $class
     * @return string
     */
    public static function name($class)
    {
        return base64_encode($class);
    }

    /**
     * 获取名称
     * @return string
     */
    public function getName()
    {
        $config = $this->config;
        if (isset($config['name'])) {
            $name = $config['name'];
        } else {
            $name = static::name($config['class']);
        }
        return $name;
    }

    /**
     * 获取类名
     * @return string
     */
    public function getClass()
    {
        $config = $this->config;
        $class  = $config['class'];
        return $class;
    }

    /**
     * 获取属性
     * @return array
     */
    public function getProperties()
    {
        $config     = $this->config;
        $properties = $config['properties'] ?? [];
        return $properties;
    }

    /**
     * 创建实例
     * @return mixed
     */
    public function newInstance()
    {
        $class      = $this->getClass();
        $properties = $this->getProperties();
        $properties = Injector::build($this->beans, $this->container, $properties);
        return new $class($properties);
    }

}
