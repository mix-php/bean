<?php

namespace Mix\Bean;

use Mix\Bean\Exception\BeanException;
use Mix\Core\Application;

/**
 * Class Beans
 * @package Mix\Bean
 * @author liu,jian <coder.keda@gmail.com>
 */
class Beans
{

    /**
     * @var Application
     */
    public $app;

    /**
     * Bean配置
     * @var array
     */
    public $config = [];

    /**
     * Bean配置缓存
     * @var array
     */
    protected $_cache;

    /**
     * Beans constructor.
     */
    public function __construct($app, $config)
    {
        // 导入属性
        $this->app    = $app;
        $this->config = $config;
        // 初始化
        $this->_cache = static::parse($config);
    }

    /**
     * 解析配置
     * @return array
     */
    protected static function parse($config)
    {
        // 解析并缓存配置
        $cache = [];
        foreach ($config as $item) {
            if (!isset($item['class'])) {
                continue;
            }
            if (isset($item['name'])) {
                $name = $item['name'];
            } else {
                $name = self::name($item['class']);
            }
            $cache[$name] = $item;
        }
        return $cache;
    }

    /**
     * 获取Bean
     * @param $beanName
     * @return Bean
     */
    public function bean($beanName)
    {
        if (!isset($this->_cache[$beanName])) {
            if (self::isBase64($beanName)) {
                $class = base64_decode($beanName);
            }
            throw new BeanException("Bean configuration not found: {$class}");
        }
        return new Bean($this->app, $this->_cache[$beanName]);
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
     * 判断是否为Base64
     * @param $str
     * @return bool
     */
    protected static function isBase64($str)
    {
        return $str == base64_encode(base64_decode($str)) ? true : false;
    }

}
