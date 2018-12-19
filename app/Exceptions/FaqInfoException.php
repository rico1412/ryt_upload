<?php

namespace App\Exceptions;

/**
 * 问答信息异常信息
 *
 * @notice 错误码为6位数，以数字 5 开头
 * @author 秦昊
 */
class FaqInfoException extends BaseException
{
    /**
     * @var array
     */
    protected static $codeMaps = [
        500001 => [
            'message' => 'invalid share link.'
        ],
        500002 => [
            'message' => '问答信息不存在'
        ],
    ];
}