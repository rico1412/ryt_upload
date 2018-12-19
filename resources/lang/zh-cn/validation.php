<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */
    'required'                 => ':attribute不能为空！',
    'required_if'                 => '请输入正确的:attribute',
    'in'                       => ':attribute无效',
    'string'                   => ':attribute必须是字符类型',
    'max.string'               => ':attribute长度不能超过:max个文字',
    'max'                      => [
        'numeric' => ':attribute不能超过:max cm',
        'string' => ':attribute最大长度:max！',
    ],
    'min'                      => [
        'numeric' => ':attribute最小长度:min！',
    ],
    'numeric'                  => ':attribute必须是数字',
    'integer'                  => ':attribute必须是整数！',
    'unique'                   => ':attribute已被使用',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */
    'custom' => [
        'thumb'        => [
            'required' =>  '请上传缩略图！',
        ],
        'original'        => [
            'required' =>  '请上传原图！',
        ],
        'title'  =>  [
            'character_length' => '标题长度不能超过30个文字！'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */
    'attributes' => [
        'name'    => '名称',
        'email'    => '邮箱',
        'status'    => '状态',
        'sort'  => '权重',
        'start_time' => '上架时间',
        'end_time' => '下架时间',
    ],
];
