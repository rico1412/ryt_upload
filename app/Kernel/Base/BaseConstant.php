<?php

namespace App\Kernel\Base;

/**
 * 常量类基类
 *
 * @author 51004
 */
abstract class BaseConstant
{
    /**
     * @return array
     */
    public static function all()
    {
        return array_keys(static::getNames());
    }

    /**
     * @return mixed
     */
    public abstract static function getNames();

    /**
     * @param $code
     * @return mixed
     */
    public static function getName($code)
    {
        return array_get(static::getNames(), $code, '');
    }

    /**
     * @param $code
     * @return bool
     */
    public static function has($code)
    {
        return array_key_exists($code, static::getNames());
    }
}