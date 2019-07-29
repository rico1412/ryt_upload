<?php

namespace App\Console\Commands;

use App\Modules\Upload\Model\WorkTime;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * 用于测试
 *
 * @author evan766
 */
class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:test';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '用于测试代码';

    
    /**
     * Test constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
    }

    
    public function handle()
    {
        // DO SOME TEST
    }
    
}