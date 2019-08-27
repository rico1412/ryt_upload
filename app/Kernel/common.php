<?php

if (! function_exists('trim_any'))
{
    /**
     * 去除所有的空格
     * @author kkk
     * @param $data
     * @param $charList
     * @return mixed
     */
    function trim_any($data, $charList = " \t\n\r\0\x0B")
    {
        if (is_string($data)) return trim($data, $charList);
        
        if (is_array($data) || is_object($data))
        {
            foreach ($data as $key => $value)
            {
                $data[$key] = trim_any($value, $charList);
            }
            
            return $data;
        }
        
        return $data;
    }
}

if (! function_exists('removeBOM'))
{
    /**
     * 检查字符串是否指定字符集
     *
     * @param string $text
     * @param array $encodings
     * @return array|bool
     */
    function removeBOM(string $text, array $encodings = [])
    {
        $targetEncoding = 'UTF-8';
        
        if (mb_check_encoding($text, $targetEncoding)) return $text;
        
        $encodings = array_merge(['ASCII,UTF-8', 'ISO-8859-1'], $encodings);
        $encodings = implode(',', $encodings);
        $text      = mb_convert_encoding($text, $targetEncoding, $encodings);
        
        if (substr($text, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF))
        {
            $text = substr($text, 3);
        }
        
        return $text;
    }
}

if (! function_exists('json_strict_decode'))
{
    /**
     * 重新封装json解码函数,可防止因为编码而造成得解码失败
     *
     * @author jianwei
     * @param string  $json   需要格式化的数据
     * @param array $encodings
     * @return array
     */
    function json_strict_decode($json, array $encodings = [])
    {
        $json = trim_any($json);
        
        if (! is_string($json) || empty($json)) return [];
        
        $json = removeBOM($json, $encodings);
        $data = json_decode($json, true);
        
        if (empty($data)) return [];
        
        return $data;
    }
}

if (! function_exists('version'))
{
    /**
     * 版本号
     *
     * @author  kkk
     * @param  boolean $build
     * @notice 当 $build 为 true 时生成新的版本号
     * @return string
     */
    function version($build = false)
    {
        static $version = null;
        
        if ($build == true || $version == null)
        {
            $version = str_random(32);
        }
        
        return $version;
    }
}

if (! function_exists('log_channel'))
{
    /**
     * 日志的频道名称
     *
     * @author  jianwei
     * @param string $channel 频道的名称
     * @return string
     */
    function log_channel($channel = null)
    {
        static $name = '';
        
        if (! empty($channel)) $name = $channel;
        
        return $name;
    }
}

if (! function_exists('get_now'))
{
    /**
     * 获取当前时间
     *
     * @author  jianwei
     * @param bool $refresh
     * @return int
     */
    function get_now($refresh = false)
    {
        static $now = null;
        
        if(! $now || $refresh) $now = time();
        
        return $now;
    }
}

if (!function_exists('get_http_host'))
{
    /**
     * 获取请求的域名
     * @return string
     */
    function get_http_host()
    {
        return  app('request')->server('HTTP_HOST') ?: array_get($_SERVER, 'HTTP_HOST', '');
    }
}

if (! function_exists('cfb_image_url'))
{
    /**
     * @param string $id
     * @return string
     */
    function cfb_image_url(string $id)
    {
        return config('api.image.host') . '/' .config('api.image.api.show') . '/' . $id . '.jpg';
    }
}
