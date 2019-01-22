<?php

namespace App\Modules\Upload\Model;

use App\Kernel\Traits\ModelTimeTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 *
 * @author 秦昊
 * Date: 2018/12/19 17:21
 */
class WorkTime extends Model
{
    use SoftDeletes, ModelTimeTraits;

    /**
     * @var string
     */
    protected $table = 'work_time';

    /**
     * @var array
     */
    protected $fillable = [
        'bank_code',
        'project_name',
        'on_duty_time',
        'off_duty_time',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'id'                => 'integer',
        'bank_code'         => 'string',
        'project_name'      => 'string',
        'on_duty_time'      => 'integer',
        'off_duty_time'     => 'integer',

        'add_time'          => 'integer',
        'last_update_time'  => 'integer',
        'created_at'        => 'timestamp',
        'updated_at'        => 'timestamp',
        'deleted_at'        => 'timestamp',
    ];

    /**
     * $var array
     */
    protected $hidden = [
        'add_time',
        'last_update_time',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'on_duty_time_str',
        'off_duty_time_str',
    ];

    /**
     * 返回格式化的上班时间
     *
     * @author 秦昊
     * Date: 2018/12/27 14:00
     * @return string
     */
    public function getOnDutyTimeStrAttribute()
    {
        return second_to_time($this->on_duty_time);
    }

    /**
     * 返回格式化的下班时间
     *
     * @author 秦昊
     * Date: 2018/12/27 14:00
     * @return string
     */
    public function getOffDutyTimeStrAttribute()
    {
        return second_to_time($this->off_duty_time);
    }

    /**
     * 工作日迟到
     *
     * @author 秦昊
     * Date: 2018/12/20 15:38
     * @param $onDutyTime
     * @return float
     */
    public function getLateTime($onDutyTime)
    {
        $onDutyTime = $this->formatDutyTime($onDutyTime);

        if (($value = $onDutyTime - $this->on_duty_time) > 0)
        {
            return floor($value / 60);
        }

        return '';
    }

    /**
     * 格式化上班时间
     *
     * @author 秦昊
     * Date: 2018/12/20 16:09
     * @param $dutyTime
     * @return float|int
     */
    private function formatDutyTime($dutyTime)
    {
        return ($dutyTime + 8 * 3600) % (3600 * 24);
    }

    /**
     * 工作日加班
     * 加班到21点后才算加班
     *
     * @author 秦昊
     * Date: 2018/12/20 15:38
     * @param $offDutyTime
     * @return float
     */
    public function getNormalOverTime($offDutyTime)
    {
        $offDutyTime = $this->formatDutyTime($offDutyTime);

        if ($offDutyTime >= (21 * 3600) && ($value = ($offDutyTime - $this->off_duty_time) / 3600 - 1) > 0)
        {
            $mValue = $value - floor($value);

            $m      = $mValue < 0.5 ? 0 : 0.5;

            return floor($value) + $m;
        }

        return '';
    }

    /**
     * 周末加班
     *
     * @author 秦昊
     * Date: 2018/12/20 15:38
     * @param $onDutyTime
     * @param $offDutyTime
     * @return float|int
     */
    public function getWeekendOverTime($onDutyTime, $offDutyTime)
    {
        $overTime   = ($offDutyTime - $onDutyTime) / 3600;

        $mValue     = $overTime - floor($overTime);

        $m          = $mValue < 0.5 ? 0 : 0.5;

        $res        = floor($overTime) + $m;

        return $res > 0 ? $res : '';
    }

    /**
     * 获取考勤状态
     *
     * @author 秦昊
     * Date: 2018/12/20 16:33
     * @param $onDutyTime
     * @param $offDutyTime
     * @return string
     */
    public function getStatus($onDutyTime, $offDutyTime)
    {
        $onDutyTime     = $this->formatDutyTime($onDutyTime);
        $offDutyTime    = $this->formatDutyTime($offDutyTime);

        if (
            $onDutyTime == $offDutyTime
            || $onDutyTime > ($this->on_duty_time + 2 * 3600) // 迟到两个小时以上
            || $offDutyTime < $this->off_duty_time
        )
        {
            return '异常';
        }

        return '';
    }

    /**
     * 根据项目代号查询
     *
     * @author 秦昊
     * Date: 2018/12/19 17:35
     * @param $query
     * @param $value
     * @return mixed
     */
    public function scopeBankCodeQuery($query, $value)
    {
        return $query->where('bank_code', $value);
    }

}