<?php

namespace App\Modules\Upload\Business;

use App\Exceptions\AppException;
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
        $res    = $this->workTimeDao->destroy($id);




        return $res;
    }

    /**
     *
     *
     * @author 秦昊
     * Date: 2018/12/28 12:51
     * @param $id
     * @param array $params
     * @return mixed
     * @throws \App\Exceptions\FaqInfoException
     * @throws \Throwable
     */
    public function updateBankInfo($id, array $params)
    {
        app('validator')->make($params, [
            'bank_code'             => 'string',
            'project_name'          => 'string',
            'on_duty_time_str'      => 'regex:/^[0-1][0-9]:[0-5][0-9]$/',
            'off_duty_time_str'     => 'regex:/^1[7-9]:[0-5][0-9]$/',
        ], [
            'bank_code.min'             => '项目别名不能为空',
            'project_name.min'          => '项目名不能为空',
            'on_duty_time_str.regex'    => '上班时间格式错误',
            'off_duty_time_str.regex'   => '下班时间格式错误',
        ])->validate();

        throw_if(
            (array_key_exists('bank_code', $params) && empty($params['bank_code'])),
            AppException::class,
            100003
        );

        if (array_key_exists('on_duty_time_str', $params)
            || array_key_exists('off_duty_time_str', $params))
        {
            if ($onDutyTimeStr  = array_pull($params, 'on_duty_time_str'))
            {
                $onDutyTime = time_to_second($onDutyTimeStr);
                $params['on_duty_time'] = $onDutyTime;

            } else if ($offDutyTimeStr = array_pull($params, 'off_duty_time_str'))
            {
                $offDutyTime = time_to_second($offDutyTimeStr);
                $params['off_duty_time'] = $offDutyTime;
            }
        }

        return $this->workTimeDao->update($id, $params);
    }

}