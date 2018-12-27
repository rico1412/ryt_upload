<?php

// 主页
$router->get('/', 'IndexController@index');

// export
$router->post('export', 'UploadController@export');

// 获取项目信息列表
$router->get('bank/info/list', 'IndexController@getBankInfoList');

// 删除项目信息
$router->get('bank/info/del', 'IndexController@delBankInfo');

