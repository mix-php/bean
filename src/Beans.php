<?php

namespace Mix\Bean;

use Mix\Bean\Exception\BeanException;
use Psr\Container\ContainerInterface;

/**
 * Class Beans
 * @package Mix\Bean
 * @author liu,jian <coder.keda@gmail.com>
 */
class Beans
{

    /**
     * @var ContainerInterface
     */
    public $container;

    /**
     * Bean配置
     * @var array
     */
    public $config = [];

    /**
     * Bean数组
     * @var Bean[]
     */
    protected $_items = [];

    /**
     * Beans constructor.
     * @param $config
     */
    public function __construct($config)
    {
        // 导入属性
        $this->container = $config['container'];
        $this->config    = $config['config'];
        // 构建
        $this->build();
    }

    /**
     * 构建
     * @return array
     */
    protected function build()
    {
        $items = [];
        foreach ($this->config as $item) {
            $bean         = new Bean([
                'beans'     => $this,
                'container' => $this->container,
                'config'    => $item,
            ]);
            $name         = $bean->getName();
            $items[$name] = $bean;
        }
        $this->_items = $items;
    }

    /**
     * 获取Bean
     * @param $beanName
     * @return Bean
     */
    public function bean($beanName)
    {
        if (!isset($this->_items[$beanName])) {
            if (self::isBase64($beanName)) {
                $class = base64_decode($beanName);
            }
            throw new BeanException("Bean configuration not found: {$class}");
        }
        return $this->_items[$beanName];
    }

    /**
     * 新增Bean
     * @param Bean $bean
     * @return bool
     */
    public function append(Bean $bean)
    {
        if (!is_object($bean->beans) || !static::compareObjects($bean->beans, $this)) {
            $class = get_class($this);
            throw new BeanException("Bean property 'beans' is not the '{$class}'");
        }
        if (!is_object($bean->container) || !static::compareObjects($bean->container, $this->container)) {
            $class = get_class($this->container);
            throw new BeanException("Bean property 'container' is not the '{$class}'");
        }
        $name                = $bean->getName();
        $this->_items[$name] = $bean;
        return true;
    }

    /**
     * 对象比较
     * @param $o1
     * @param $o2
     * @return bool
     */
    protected static function compareObjects(&$o1, &$o2)
    {
        return $o1 === $o2;
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
