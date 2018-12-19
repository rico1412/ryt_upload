<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Handler\AmqpHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Processor\WebProcessor;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class LoggerServiceProvider extends ServiceProvider
{
    
    //延时加载
    public $defer = true;
    
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
    
    
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Logger', function ($app) {
            return $this->createLogger($app);
        });
    }
    
    /**
     * @param $app
     * @return Logger
     * @throws \Exception
     */
    public function createLogger($app)
    {
        //以域名为为channel_name
        $channelName = log_channel();
        if(empty($channelName)) {
            $channelName = log_channel($app['request']->getHttpHost());
        }
        $logger = new Logger($channelName);
        
        //添加process，为日志添加额外的数据或格式化数据
        $logger->pushProcessor(new WebProcessor());
        $logger->pushProcessor(new PsrLogMessageProcessor());
        
        //当为测试环境，把内存使用什么的都记录下来
        if(config('app.debug') === true){
            $logger->pushProcessor(new MemoryUsageProcessor());
        }
        
        //生成一个版本号
        version(true);
        
        //额外记录的消息
        $messages = array();
        $messages['ip'] = $app['request']->ip();
        $messages['request'] = $app['request']->all();
        $messages['full_url'] = $app['request']->fullUrl();
        $messages['request_time'] = get_now();
        
        //自定义的processer,记录一些需要的信息
        $logger->pushProcessor(function ($record) use ($app,$messages) {
            //$record['version'] = $version;
            //$record['messages'] = $messages;
            $record['extra']['version'] = version();
            $record['extra']['messages'] = $messages;
            $record['extra']['request_time'] = get_now(true);
            
            foreach($messages as $sk=>$sv){
                $record['extra'][$sk] = $sv;
            }
            
            return $record;
        });
        
        $type = array_map('trim', explode(',', config('logger.handle_type', 'file')));
        
        // 文件
        if (in_array('file', $type)) {
            $logger->pushHandler(new StreamHandler(config('logger.file'), config('logger.level')));
        }
        
        return $logger;
    }
    
    
    /**
     * Get the maximum number of log files for the application.
     *
     * @return int
     */
    protected function maxFiles()
    {
        if ($this->app->bound('config')) {
            return $this->app->make('config')->get('app.log_max_files', 5);
        }
        
        return 0;
    }
    
    public function provides()
    {
        return [
            'Logger',
        ];
    }
}
