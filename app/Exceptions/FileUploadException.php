<?php

namespace App\Exceptions;

/**
 * 文件上传异常
 * Class FileUploadException
 * @package App\Exceptions
 */
class FileUploadException extends BaseException
{
    protected static $codeMaps = [
        600000 => [
            'message' => 'file invalid.',
        ],
        600001 => [
            'message' => '数据有误！',
        ],
        600002 => [
            'message' => '表格数据不能为空！',
        ],
        600003 => [
            'message' => '表头数据错误！',
        ],
        600004 => [
            'message' => '文件格式错误！',
        ],
        600005 => [
            'message' => '上传的文件名与项目代号不一致！',
        ],
    ];
}
