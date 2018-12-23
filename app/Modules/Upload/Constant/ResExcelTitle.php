<?php

namespace App\Modules\Upload\Constant;

use App\Kernel\Base\BaseConstant;

/**
 *
 *
 * @author 秦昊
 * Date: 2018/12/19 17:50
 */
class ResExcelTitle extends BaseConstant
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
     * 星期
     */
    public const WEEK           = 'week';

    /**
     * 工作日期
     */
    public const DAY            = 'day';

    /**
     * 上班时间
     */
    public const ON_DUTY_TIME   = 'on_duty_time';

    /**
     * 下班时间
     */
    public const OFF_DUTY_TIME  = 'off_duty_time';

    /**
     * 加班时间（小时）
     */
    public const OVER_TIME      = 'over_time';

    /**
     * 迟到时间（分）
     */
    public const LATE_TIME      = 'late_time';

    /**
     * 状态
     */
    public const STATUS         = 'status';

    /**
     * 上班天数
     */
    public const WORK_DAYS      = 'work_days';

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
            self::WEEK          => '星期',
            self::DAY           => '工作日期',
            self::ON_DUTY_TIME  => '上班时间',
            self::OFF_DUTY_TIME => '下班时间',
            self::OVER_TIME     => '加班时间（小时）',
            self::LATE_TIME     => '迟到时间（分）',
            self::STATUS        => '状态',
            self::WORK_DAYS     => '上班天数',
        ];
    }
}