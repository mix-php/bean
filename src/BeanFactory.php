<?php

namespace Mix\Bean;

use Mix\Bean\Exception\BeanException;

/**
 * Class BeanFactory
 * @package Mix\Bean
 * @author liu,jian <coder.keda@gmail.com>
 */
class BeanFactory
{

    /**
     * Bean配置
     * @var array
     */
    public $config = [];

    /**
     * Bean数组
     * @var BeanDefinition[]
     */
    protected $_definitions = [];

    /**
     * 单例池
     * @var array
     */
    protected $_objects = [];

    /**
     * Beans constructor.
     * @param $config
     */
    public function __construct(array $config)
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
            $bean         = new BeanDefinition([
                'beanFactory' => $this,
                'config'      => $item,
            ]);
            $name         = $bean->getName();
            $items[$name] = $bean;
        }
        $this->_items = $items;
    }

    /**
     * 获取BeanDefinition
     * @param $beanName
     * @return BeanDefinition
     */
    public function getBeanDefinition(string $beanName)
    {
        if (!isset($this->_items[$beanName])) {
            throw new BeanException("Bean configuration not found: {$beanName}");
        }
        return $this->_items[$beanName];
    }

    /**
     * 获取Bean
     * @param $beanName
     * @param array $config
     * @return object
     */
    public function getBean(string $beanName, array $config = [])
    {
        $beanDefinition = $this->getBeanDefinition($beanName);
        // singleton
        if ($beanDefinition->getScope() == BeanDefinition::SINGLETON) {
            if (isset($this->_objects[$beanName])) {
                return $this->_objects[$beanName];
            }
            $object                    = $beanDefinition->newInstance($config);
            $this->_objects[$beanName] = $object;
            return $object;
        }
        // prototype
        $object = $beanDefinition->newInstance($config);
        return $object;
    }

    /**
     * 注册BeanDefinition
     * @param BeanDefinition $beanDefinition
     * @return bool
     */
    public function registerBeanDefinition(BeanDefinition $beanDefinition)
    {
        $name                      = $beanDefinition->getName();
        $this->_definitions[$name] = $beanDefinition;
        return true;
    }

}
