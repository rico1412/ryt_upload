<?php

namespace App\Modules\Upload\Business;

use App\Kernel\Base\BaseBusiness;
use App\Modules\Upload\Dao\WorkTimeDao;

/**
 *
 *
 * @author 秦昊
 * Date: 2018/12/21 22:50
 */
class ProjectBusiness extends BaseBusiness
{
    /**
     * @var WorkTimeDao
     */
    protected $workTimeDao;

    /**
     * ProjectBusiness constructor.
     * @param WorkTimeDao $workTimeDao
     */
    public function __construct(WorkTimeDao $workTimeDao)
    {
        $this->workTimeDao = $workTimeDao;
    }

    /**
     * 获取所有项目信息
     *
     * @author 秦昊
     * Date: 2018/12/26 15:44
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getBankInfoList(array $columns = ['*'])
    {
        return $this->workTimeDao->all($columns);
    }

    /**
     *
     *
     * @author 秦昊
     * Date: 2018/12/21 22:51
     * @param array $projectList
     */
    public function import(array $projectList)
    {
        // check
        foreach ($projectList as $projectInfo)
        {
            app('validator')->make($projectInfo, [
                'bank_code'     => 'required',
                'on_duty_time'  => 'required',
                'off_duty_time' => 'required',
                'project_name'  => 'required',
            ])->validate();
        }

        // format
        foreach ($projectList as &$projectInfo)
        {
            $projectInfo['on_duty_time']    = time_to_second($projectInfo['on_duty_time']);
            $projectInfo['off_duty_time']   = time_to_second($projectInfo['off_duty_time']);
            $projectInfo['add_time']        = get_now();
            $projectInfo['last_update_time']= get_now();
            $projectInfo['created_at']      = date('Y-m-d H:i:s');
            $projectInfo['updated_at']      = date('Y-m-d H:i:s');
//            dd($projectInfo);
        }

        $this->workTimeDao->syncProjects($projectList);
    }

    /**
     * 删除项目信息
     *
     * @author 秦昊
     * Date: 2018/12/27 13:59
     * @param $id
     * @return mixed
     */
    public function delBankInfo($id)
    {
        return $this->workTimeDao->destroy($id);
    }

}