<?php

// 主页
$router->get('/', 'IndexController@index');

// export
$router->post('export', 'UploadController@export');

