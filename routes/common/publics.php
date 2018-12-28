<?php

// 主页
$router->get('/', 'IndexController@index');

// export
$router->post('export', 'UploadController@export');

// 项目相关
$router->group(['prefix' => 'bank/info'], function ($router)
{
    // 获取项目信息列表
    $router->get('list', 'IndexController@getBankInfoList');

    // 删除项目信息
    $router->get('del', 'IndexController@delBankInfo');
    $router->post('update', 'IndexController@updateBankInfo');
});



