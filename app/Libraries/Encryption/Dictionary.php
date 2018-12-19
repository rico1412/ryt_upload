<?php

namespace App\Libraries\Encryption;

use App\Libraries\LibrariesInterface;
use function GuzzleHttp\Psr7\build_query;

/**
 * 加密方法封装
 *
 * @package App\Http\Common
 */
class Dictionary implements LibrariesInterface
{
    public static function usage()
    {
        // TODO: Implement usage() method.
    }
    
    /**
     * 加密
     *
     * @param array $params 参数
     * @param string $key 密钥
     * @param array $expect
     * @return string
     */
    public function encrypt(array $params, $key, array $expect = [])
    {
        $params = array_except($params, $expect);

        $params = $this->filterParams($params);

        ksort($params);

        $params['_key'] = $key;

        return strtoupper(md5($this->paramToString($params)));
    }

    /**
     * 将数组参数转化为字符串
     *
     * @param array $params
     * @return string
     */
    protected function paramToString(array $params)
    {
        $data = [];

        foreach ($params as $k => $v)
        {
            $data[] = $k . "=" . $v;
        }

        return implode('&', $data);
    }

    /**
     * 过滤所有参数
     *
     * @param array $params
     * @return array
     */
    protected function filterParams(array $params)
    {
        $data = [];

        foreach ($params as $key => $val)
        {
            if (is_string($val) || is_int($val))
            {
                $data[$key] = $this->filter($val);
            }
        }

        return $data;
    }

    /**
     * 过滤数组
     *
     * @param $key
     * @param array $params
     * @return array
     */
    protected function filterArray($key, array $params)
    {
        $data = [];

        foreach ($params as $k => $v)
        {
            if (is_array($v))
            {
                $data = array_merge($data, $this->filterArray($key . '[' . $k . ']', $v));
            }
            else
            {
                $data[$key . '[' . $k .']'] = $this->filter($v);
            }
        }

        return $data;
    }

    /**
     * 过滤字符
     *
     * @todo 微信android客户端会把'带到链接参数上面
     * @param $string
     * @return mixed
     */
    protected function filter($string)
    {
        $filterStr = "'\t\n\r \v";

        return trim_any($string, $filterStr);
    }
    
    /**
     * 用于过滤器判断是否合法
     *
     * @param $key
     * @param array $params
     * @return bool
     */
    public function validate($key, array $params)
    {
        $validate = app('validator')->make($params, [
            '_ts'       => 'required|numeric',
            '_rd'       => 'required',
            '_terminal' => 'required',
            '_sign'     => 'required',
        ]);

        if ($validate->fails()) return false;

        $sign = array_pull($params, '_sign');

        return $this->encrypt($params, $key) == $sign;
    }
}
