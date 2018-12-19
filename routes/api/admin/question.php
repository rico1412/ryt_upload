<?php

$router->group(['prefix' => 'question'], function ($router)
{
    $router->group(['prefix' => 'info'], function ($router)
    {
        $router->get('list',        'QuestionController@list');     // 后台列表
        $router->post('add',        'QuestionController@add');      // 新增
        $router->post('update',     'QuestionController@update');   // 编辑
        $router->get('show',        'QuestionController@show');     // 获取
        $router->get('del',         'QuestionController@del');      // 删除
    });

    $router->group(['prefix' => 'tag'], function ($router)
    {
        $router->get('list',        'FaqTagController@list');       // 标签列表
        $router->get('page/list',   'FaqTagController@pageList');   // 标签分页列表
        $router->post('add',        'FaqTagController@add');        // 新增标签
        $router->get('del',         'FaqTagController@del');        // 删除标签
    });
});
