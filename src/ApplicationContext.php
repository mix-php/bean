<?php

namespace Mix\Bean;

/**
 * Class ApplicationContext
 * @package Mix\Bean
 * @author liu,jian <coder.keda@gmail.com>
 */
class ApplicationContext implements BeanFactoryInterface
{

    use BeanFactoryTrait;

    /**
     * ApplicationContext constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        // 导入属性
        $this->config = $config;
        // 初始化
        $this->init();
    }

}