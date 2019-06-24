<?php

namespace Mix\Bean;

use Mix\Bean\Exception\InjectException;

/**
 * Class BeanInjector
 * @package Mix\Bean
 * @author liu,jian <coder.keda@gmail.com>
 */
class BeanInjector
{

    /**
     * 构建
     * @param BeanFactory $beanFactory
     * @param array $config
     * @return array
     */
    public static function build(BeanFactory $beanFactory, array $config)
    {
        foreach ($config as $key => $value) {
            // 子类处理
            if (is_array($value)) {
                if (array_values($value) === $value) {
                    // 非关联数组
                    foreach ($value as $subNumberKey => $subValue) {
                        if (isset($subValue['ref'])) {
                            $config[$key][$subNumberKey] = static::build($beanFactory, $subValue);
                        }
                    }
                } else {
                    // 引用依赖
                    if (isset($value['ref'])) {
                        $config[$key] = static::build($beanFactory, $value);
                    }
                }
            } elseif ($key === 'ref') {
                // 引用依赖实例化
                return $beanFactory->getBean($config['ref']);
            }
        }
        return $config;
    }

    /**
     * 注入属性
     * @param $object
     * @param array $properties
     * @return ObjectInterface
     * @throws \ReflectionException
     */
    public static function inject($object, array $properties)
    {
        foreach ($properties as $name => $value) {
            // 导入
            $object->$name = $value;
            // 注释类型检测
            $class      = get_class($object);
            $reflection = new \ReflectionClass($class);
            if (!$reflection->hasProperty($name)) {
                continue;
            }
            $docComment = $reflection->getProperty($name)->getDocComment();
            $var        = self::var($docComment);
            if (!$var) {
                continue;
            }
            if (substr($var, -2) === '[]') {
                // 当前的doc标注里面这是一个数组，去掉数组的尾巴
                $var = substr($var, 0, -2);
                // 这时候当前的$value已经是个被依赖注入自动维护的实例数组了 不需要特殊处理
            } else {
                // 不是数组，弄成临时数组 方便下面遍历检查
                $value = [$value];
            }
            if (!interface_exists($var) && !class_exists($var)) {
                throw new InjectException("Interface or class not found, class: {$class}, property: {$name}, @var: {$var}");
            }
            foreach ($value as $v) {
                if (!($v instanceof $var)) {
                    throw new InjectException("The type of the imported property does not match, class: {$class}, property: {$name}, @var: {$var}");
                }
            }
        }
        return $object;
    }

    /**
     * 获取注释中var的值
     * @param $docComment
     * @return string
     */
    protected static function var($docComment)
    {
        $var = '';
        if (!$docComment) {
            return $var;
        }
        $key   = '@var';
        $len   = 4;
        $start = strpos($docComment, $key);
        $end   = strpos($docComment, '*', $start + $len);
        if ($start !== false && $end !== false) {
            $tmp = substr($docComment, $start + $len, $end - $start - $len);
            $tmp = explode(' ', trim($tmp));
            $var = array_shift($tmp);
            $var = substr($var, 0, 1) === '\\' ? substr($var, 1) : '';
        }
        return $var;
    }

}
