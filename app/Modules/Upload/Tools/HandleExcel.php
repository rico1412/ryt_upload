<?php

namespace App\Modules\Upload\Tools;

use App\Exceptions\FileUploadException;
use App\Modules\Upload\Constant\OriginExcelTitle;
use App\Modules\Upload\Constant\ResExcelTitle;
use App\Modules\Upload\Constant\Week;
use Illuminate\Http\UploadedFile;

/**
 *
 *
 * @author 秦昊
 * Date: 2018/12/23 15:27
 */
class HandleExcel
{
    /**
     * 获取原始Excel解析后的数据
     *
     * @author 秦昊
     * Date: 2018/12/23 11:52
     * @param UploadedFile $file
     * @return array
     * @throws FileUploadException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function getParseData(UploadedFile $file)
    {
        $filePath       = get_file_path($file);
        $fileExt        = $file->getClientOriginalExtension();

        $titleMap       = array_flip(OriginExcelTitle::getNames());

        // 解析Excel数据
        $parseExcel     = new ParseExcel($filePath, $fileExt, $titleMap);
        $workData       = $parseExcel->getFirstSheetData();

//        dd($workData);
//        dd($parseExcel->getWorkDayList());

        return $workData;
    }

    /**
     * 获取最终要导出的数据
     *
     * @author 秦昊
     * Date: 2018/12/21 09:11
     * @param $bankInfo
     * @param array $parseData
     * @return array
     */
    public function getResExcelData($bankInfo, array $parseData)
    {
        $workTempData   = [];
        $onDutyTimeArr  = [];

        foreach ($parseData as $workInfo)
        {
            $dayTime    = $workInfo[OriginExcelTitle::DAY_TIME];
            $name       = $workInfo[OriginExcelTitle::NAME];
            $dutyTime   = $workInfo[OriginExcelTitle::DUTY_TIME];

            $resWorkInfo                                = [];
            $resWorkInfo[ResExcelTitle::NAME]           = $name;
            $resWorkInfo[ResExcelTitle::JOB_NUM]        = $workInfo[OriginExcelTitle::JOB_NUM];
            $resWorkInfo[ResExcelTitle::PROJECT_NAME]   = array_get($workInfo, OriginExcelTitle::PROJECT_NAME);
            $resWorkInfo[ResExcelTitle::WEEK]           = get_week($dayTime);
            $resWorkInfo[ResExcelTitle::DAY]            = $dayTime;

            if (!array_key_exists($name, $workTempData))
            {
                $workTempData[$name] = [];
            }

            if (!array_key_exists($dayTime, $workTempData[$name]))
            {
                $onDutyTimeArr[$name][$dayTime]             = $dutyTime;
                $resWorkInfo[ResExcelTitle::ON_DUTY_TIME]   = $dutyTime;
                $resWorkInfo[ResExcelTitle::OFF_DUTY_TIME]  = $dutyTime;
            } else {
                $resWorkInfo[ResExcelTitle::ON_DUTY_TIME]   = $onDutyTimeArr[$name][$dayTime];
                $resWorkInfo[ResExcelTitle::OFF_DUTY_TIME]  = $dutyTime;
            }

            $workTempData[$name][$dayTime] = $resWorkInfo;
        }

//        dd($workTempData);

        $resWorkData = [];

        foreach ($workTempData as $workTempItem)
        {
            $workDayCount = count($workTempItem);

            foreach ($workTempItem as $dayTime => $workInfo)
            {
                $itemOnDutyTime = $workInfo[ResExcelTitle::ON_DUTY_TIME];
                $workInfo[ResExcelTitle::ON_DUTY_TIME]  = date('H:i:s', $itemOnDutyTime);

                $itemOffDutyTime= $workInfo[ResExcelTitle::OFF_DUTY_TIME];
                $workInfo[ResExcelTitle::OFF_DUTY_TIME] = date('H:i:s', $itemOffDutyTime);

                $week           = $workInfo[ResExcelTitle::WEEK];

                $overTime   = 0;
                $lateTime   = 0;
                $status     = '';
                switch ($week)
                {
                    case Week::Saturday:
                    case Week::Sunday:
                        // 周末只计算加班
                        $overTime = $bankInfo->getWeekendOverTime($itemOnDutyTime, $itemOffDutyTime);
                        break;
                    case Week::Monday:
                    case Week::Tuesday:
                    case Week::Wednesday:
                    case Week::Thursday:
                    case Week::Friday:
                        // 加班
                        $overTime   = $bankInfo->getNormalOverTime($itemOffDutyTime);

                        // 计算迟到
                        $lateTime   = $bankInfo->getLateTime($itemOnDutyTime);

                        // 是否有异常
                        $status     = $bankInfo->getStatus($itemOnDutyTime, $itemOffDutyTime);
                        break;
                }

                $workInfo[ResExcelTitle::OVER_TIME] = $overTime == 0 ? '' : $overTime;
                $workInfo[ResExcelTitle::LATE_TIME] = $lateTime == 0 ? '' : $lateTime;
                $workInfo[ResExcelTitle::STATUS]    = $status;

                $workInfo[ResExcelTitle::WORK_DAYS] = $workDayCount;

                $resWorkData[]  = $workInfo;
            }
        }

//        dd($resWorkData);

        return $resWorkData;
    }
}