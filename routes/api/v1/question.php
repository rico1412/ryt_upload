<?php

$router->group(['prefix' => 'question'], function ($router)
{
    $router->get('list',            'QuestionController@list');         // 列表
    $router->get('show',            'QuestionController@show');         // 展示

    $router->get('add/copy/count',  'FaqAnswerController@addCopyCount'); // 增加答案复制次数
});
