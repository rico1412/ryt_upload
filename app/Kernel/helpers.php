<?php


if (!function_exists('get_page_size')) {

    /**
     * 获取每页默认记录数
     * User: lizhenhai
     * Date: 2018/7/20 0020
     * @param $param
     * @return int
     */
    function get_page_size($param)
    {
        $pageSize = array_get($param, 'page_size');

        return (int)$pageSize > 0 ? $pageSize : 10;
    }
}

if (!function_exists('format_content')) {

    /**
     * 格式化content
     *
     * @author 秦昊
     * Date: 2018/9/5 15:56
     * @param $content
     * @return mixed
     */
    function format_content($content)
    {
        return str_replace('；', PHP_EOL, $content);
    }
}
