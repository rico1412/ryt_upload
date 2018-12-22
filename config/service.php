<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 服务名称
    |--------------------------------------------------------------------------
    */

    'name' => 'ryt-upload-service',

    /*
    |--------------------------------------------------------------------------
    | 签名密钥
    |--------------------------------------------------------------------------
    */

    'sign_key' => env('UPLOAD_SERVICE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | 是否开启请求签名校验
    |--------------------------------------------------------------------------
    */

    'validation_sign' => env('UPLOAD_VALIDATION_SIGN', true),
];
