<?php

namespace App\Console\Commands;

use App\Modules\Upload\Business\ProjectBusiness;
use Illuminate\Console\Command;

class ImportProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:projects';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var ProjectBusiness
     */
    protected $projectBusiness;

    /**
     * ImportProjects constructor.
     * @param ProjectBusiness $projectBusiness
     */
    public function __construct(ProjectBusiness $projectBusiness)
    {
        $this->projectBusiness = $projectBusiness;

        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('开始导入');

        $this->projectBusiness->import(config('project'));

        $this->info('导入完成');
    }
}
