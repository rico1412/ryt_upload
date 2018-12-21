<?php

namespace App\Modules\Upload\Constant;

use App\Kernel\Base\BaseConstant;

/**
 *
 *
 * @author 秦昊
 * Date: 2018/12/19 17:50
 */
class OriginExcelTitle extends BaseConstant
{
    /**
     * 姓名
     */
    public const NAME           = 'name';

    /**
     * 工号
     */
    public const JOB_NUM        = 'job_num';

    /**
     * 所属项目
     */
    public const PROJECT_NAME   = 'project_name';

    /**
     * 考勤时间
     */
    public const DUTY_TIME      = 'duty_time';

    /**
     * 考勤日期
     */
    public const DAY_TIME       = 'day_time';

    /**
     * @author 秦昊
     * Date: 2018/12/19 17:54
     * @return array|mixed
     */
    public static function getNames()
    {
        return [
            self::NAME          => '姓名',
            self::JOB_NUM       => '工号',
            self::PROJECT_NAME  => '所属项目',
            self::DUTY_TIME     => '考勤时间',
        ];
    }
}