<?php

return array(
    //日志的handle,以,号隔开
    'handle_type'   =>  env('LOG_HANDLE_TYPE','file'),
    
    //记录的文件路径
    'file'  =>  storage_path('logs/mono_logger.log'),
    
    //记录的日志等级
    'level' =>  \Monolog\Logger::DEBUG,
    
    //rabbitmq设置
    'rabbitmq'  =>  [
        'host'  =>  env('LOG_RABBITMQ_HOST'),
        'port'  =>  env('LOG_RABBITMQ_PORT'),
        'username'  =>  env('LOG_RABBITMQ_USERNAME'),
        'password'  =>  env('LOG_RABBITMQ_PASSWORD'),
        'vhost'  =>  '/stocklog',
    
        //交换机的类型,主题交换机
        'log_exchange_type' =>  'topic',
        //日志的交换机名称，多个项目共用交换机，免得新增一个项目都多开启一个交换机
        'log_exchange_name' =>  'logs_exchange',
        //队列名称
        'log_queue_name'    =>  'log_queue',
        //路由名称
        'log_route_name'    =>  '#',
    ]
);
