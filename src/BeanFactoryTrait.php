<?php

namespace Mix\Bean;

use Mix\Bean\Exception\BeanException;

/**
 * Class BeanFactory
 * @package Mix\Bean
 * @author liu,jian <coder.keda@gmail.com>
 */
Trait BeanFactoryTrait
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
     * 初始化
     */
    public function init()
    {
        $definitions = [];
        foreach ($this->config as $item) {
            $bean               = new BeanDefinition([
                'beanFactory' => $this,
                'config'      => $item,
            ]);
            $name               = $bean->getName();
            $definitions[$name] = $bean;
        }
        $this->_definitions = $definitions;
    }

    /**
     * 获取BeanDefinition
     * @param $beanName
     * @return BeanDefinition
     */
    public function getBeanDefinition(string $beanName): BeanDefinition
    {
        if (!isset($this->_definitions[$beanName])) {
            throw new BeanException("Bean definition not found: {$beanName}");
        }
        return $this->_definitions[$beanName];
    }

    /**
     * 获取Bean
     * @param string $beanName
     * @param array $config
     * @return mixed
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
        return $beanDefinition->newInstance($config);
    }

}
