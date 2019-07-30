<?php

namespace App\Modules\Upload\Model;

use App\Kernel\Traits\ModelTimeTraits;
use Carbon\Carbon;
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
     * 工作日迟到（分钟）
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
        return Carbon::createFromTimestamp($dutyTime)->secondsSinceMidnight();
    }

    /**
     * 工作日加班
     * 加班到21点后才算加班
     *
     * 工作日：
     * 21:00:00-21:59:00打卡，算加班2小时；
     * 22:00:00-23:45:00打卡，算加班3小时；
     * 23:46:00-00:00:00打卡，算加班4小时。
     *
     * @author 秦昊
     * Date: 2018/12/20 15:38
     * @param $offDutyTime
     * @return float
     */
    public function getNormalOverTime($offDutyTime)
    {
        $offDutyTime = $this->formatDutyTime($offDutyTime);

        if ($offDutyTime >= (21 * 3600))
        {
            if ($offDutyTime >= Carbon::createFromTime(21)->secondsSinceMidnight()
                && $offDutyTime < Carbon::createFromTime(22)->secondsSinceMidnight())
            {
                return 2;

            } elseif ($offDutyTime >= Carbon::createFromTime(22)->secondsSinceMidnight()
                && $offDutyTime <= Carbon::createFromTime(23, 45)->secondsSinceMidnight())
            {
                return 3;

            } elseif ($offDutyTime > Carbon::createFromTime(23, 45)->secondsSinceMidnight()
                && $offDutyTime <= Carbon::createFromTime(23, 59, 59)->secondsSinceMidnight())
            {
                return 4;
            }
        }

        return '';
    }

    /**
     * 周末加班
     *
     * 周六日单日加班未跨0点情况，
     * 不满4小时不算，
     * 满4小时不满8小时按4小时计，
     * 超过9个小时按8小时计
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

        if ($overTime >= 4 && $overTime < 9)
        {
            $res = 4;

        } elseif ($overTime >= 9)
        {
            $res = 8;

        } else {

            $res = 0;
        }

        return $res ?: '';
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
        ) {
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