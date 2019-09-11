<?php

namespace app\core;

class Common
{
    /**
     * 格式化异常信息
     * @param \Exception $ex
     * @return string
     */
    public static function formatExceptionMessage(\Exception $ex)
    {
        return sprintf('%s in %s line %d', $ex->getMessage(),
            $ex->getFile(), $ex->getLine());
    }

    /**
     * 格式化异常信息
     * @param \Error $ex
     * @return string
     */
    public static function formatErrorMessage(\Error $ex)
    {
        return sprintf('%s in %s line %d', $ex->getMessage(),
            $ex->getFile(), $ex->getLine());
    }

    /**
     * 把字符串转换为UTF-8编码
     * @param string $str 要转换的字符串
     * @return mixed|string
     */
    public static function convertStrEncoding2UTF8($str)
    {
        $encode = mb_detect_encoding($str, array('ASCII', 'UTF-8', "GB2312", "GBK", 'BIG5'));
        if ($encode != 'UTF-8')
            $str = mb_convert_encoding($str, 'UTF-8', $encode);
        return $str;
    }

    /**
     * 枚举是否存在某個值
     * @param string $enumClass 枚举类名
     * @param mixed $value 值
     * @return bool
     * @throws \ReflectionException
     */
    public static function enumHasValue($enumClass, $value)
    {

        $reflection = new \ReflectionClass($enumClass);
        $instance = $reflection->newInstance();
        $method = new \ReflectionMethod($enumClass, 'getConstList');

        $enums = $method->invoke($instance);
        if (!$enums || in_array($value, $enums))
            return false;

        return true;
    }

    /**
     * 获取枚举值列表
     * @param string $enumClass 枚举类名
     * @return array
     */
    public static function getEnumValues($enumClass)
    {
        $reflection = new \ReflectionClass($enumClass);
        $instance = $reflection->newInstance();
        $method = new \ReflectionMethod($enumClass, 'getConstList');
        return $method->invoke($instance);
    }

    /**
     * 获取枚举键值对
     * @param string $enumClass 枚举类名
     * @return array
     */
    public static function getEnumKeyValuePairs($enumClass)
    {
        $reflection = new \ReflectionClass($enumClass);
        $instance = $reflection->newInstance();
        $method = new \ReflectionMethod($enumClass, 'getKeyValuePairs');
        return $method->invokeArgs($instance, []);
    }

    public static function appendUrlQueryParams($url, array $params)
    {
        if (strpos($url, '?') === false)
            return $url . '?' . http_build_query($params);
        return $url . '&' . http_build_query($params);
    }

    /**
     * 版本比较
     * @param string $version1 版本1
     * @param string $version2 版本2
     * @param string $operator 操作符(参考原生version_compare)
     * @return mixed
     */
    public static function version_compare($version1, $version2, $operator)
    {
        $version1_arr = explode('.', $version1);
        $version2_arr = explode('.', $version2);
        $max_length = max(count($version1_arr), count($version2_arr));
        $version1_arr = array_pad($version1_arr, $max_length, 0);
        $version2_arr = array_pad($version2_arr, $max_length, 0);
        return version_compare(implode('.', $version1_arr), implode('.', $version2_arr), $operator);
    }
}