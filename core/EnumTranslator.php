<?php

namespace app\core;


class EnumTranslator
{
    private static $instances = [];

    /**
     * 枚举翻译器
     * @param string $enumClass 枚举类名
     * @param string|int $value 枚举值
     * @return string
     */
    public static function Translate($enumClass, $value)
    {
        if (!isset(self::$instances[$enumClass])) {
            $reflection = new \ReflectionClass($enumClass);
            $instance = $reflection->newInstance();
            $method = new \ReflectionMethod($enumClass, 'translate');
            self::$instances[$enumClass][] = $instance;
            self::$instances[$enumClass][] = $method;
        }
        $instance = self::$instances[$enumClass][0];
        $method = self::$instances[$enumClass][1];
        return $method->invokeArgs($instance, [$value]);
    }
}