<?php

namespace App\Exceptions;

/**
 * 程序运行异常
 *
 * @notice 错误码为5位数，以数字 0 开头
 * @author 51004
 */
class RuntimeException extends BaseException
{
    /**
     * @var array
     */
    protected static $codeMaps = [
        100000 => [
            'message' => 'something wrong!',
        ],
        100001 => [
            'message' => '服务接口返回参数错误！',
        ],
        100002 => [
            'message' => '请求服务接口失败：缺少加密 key！',
        ],
        100003 => [
            'message' => '加密参数错误！',
        ],
        100004 => [
            'message' => '加密验证失败！',
        ],
    ];
}