<?php

namespace App\Exceptions;

/**
 * Auth 异常信息
 *
 * @notice 错误码为6位数，以数字 4 开头
 * @author 51004
 */
class AuthException extends BaseException
{
    /**
     * @var array
     */
    protected static $codeMaps = [
        400002 => [
            'message' => 'Unauthorized.'
        ],
    ];
}