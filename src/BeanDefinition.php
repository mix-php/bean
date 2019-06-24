<?php

namespace Mix\Bean;

use Mix\Bean\Exception\BeanException;

/**
 * Class BeanDefinition
 * @package Mix\Bean
 * @author liu,jian <coder.keda@gmail.com>
 */
class BeanDefinition
{

    /**
     * Scope
     */
    const PROTOTYPE = 'prototype';
    const SINGLETON = 'singleton';

    /**
     * @var BeanFactory
     */
    public $beanFactory;

    /**
     * @var array
     */
    public $config;

    /**
     * Beans constructor.
     * @param $config
     */
    public function __construct(array $config)
    {
        // 导入属性
        $this->beanFactory = $config['beanFactory'];
        $this->config      = $config['config'];
    }

    /**
     * 初始化后执行指定方法
     * @return string
     */
    public function getInitMethod()
    {
        $config = $this->config;
        if (isset($config['initMethod'])) {
            return $config['initMethod'];
        }
        return '';
    }

    /**
     * 获取名称
     * @return string
     */
    public function getName()
    {
        $config = $this->config;
        if (isset($config['name'])) {
            return $config['name'];
        }
        return $config['class'];
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
     * 获取作用域
     * @return string
     */
    public function getScope()
    {
        $config = $this->config;
        if (isset($config['scope'])) {
            $scope = $config['scope'];
            if (!in_array($scope, [static::PROTOTYPE, static::SINGLETON])) {
                throw new ScopeException('Scope can only be [' . static::PROTOTYPE . ', ' . static::SINGLETON . ']');
            }
            return $scope;
        }
        return static::PROTOTYPE;
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
     * 获取构造参数
     * @return array
     */
    public function getConstructorArgs()
    {
        $config     = $this->config;
        $properties = $config['constructorArgs'] ?? [];
        return $properties;
    }

    /**
     * 创建实例
     * @param $config
     * @return mixed
     */
    public function newInstance(array $config)
    {
        $class           = $this->getClass();
        $properties      = $this->getProperties();
        $constructorArgs = $this->getConstructorArgs();
        $initMethod      = $this->getInitMethod();
        if ($properties) {
            $properties = array_merge($properties, $config);
            $properties = BeanInjector::build($this->beanFactory, $properties);
            $object     = new $class();
            BeanInjector::inject($object, $properties);
        } elseif ($constructorArgs) {
            $constructorArgs = array_merge($constructorArgs, $config);
            $object          = new $class(...$constructorArgs);
        } else {
            $object = new $class();
        }
        $initMethod and call_user_func($object, $initMethod);
        return $object;
    }

}
