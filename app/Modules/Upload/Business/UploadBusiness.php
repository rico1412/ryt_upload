<?php

namespace App\Modules\Upload\Business;

use App\Exceptions\FileUploadException;
use App\Kernel\Base\BaseBusiness;
use App\Modules\Upload\Constant\OriginExcelTitle;
use App\Modules\Upload\Constant\ResExcelTitle;
use App\Modules\Upload\Constant\Week;
use App\Modules\Upload\Dao\WorkTimeDao;
use App\Modules\Upload\Tools\ParseExcel;
use Illuminate\Http\UploadedFile;

/**
 *
 *
 * @author 秦昊
 * Date: 2018/12/19 17:29
 */
class UploadBusiness extends BaseBusiness
{
    /**
     * @var WorkTimeDao
     */
    protected $workTimeDao;

    /**
     * UploadBusiness constructor.
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
     * Date: 2018/12/21 19:23
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getProjectList()
    {
        return $this->workTimeDao->getProjectList();
    }

    /**
     *
     *
     * @author 秦昊
     * Date: 2018/12/21 09:41
     * @param $bankCode
     * @param $file
     * @return array
     * @throws FileUploadException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function getResExcel($bankCode, UploadedFile $file)
    {
        $fileName   = $file->getClientOriginalName();
        $fileExt    = $file->getClientOriginalExtension();

        if ($fileName != "{$bankCode}.{$fileExt}") throw new FileUploadException(600005);

        $resExcelData   = $this->getResExcelData($bankCode, $file);

        return $resExcelData;
    }

    /**
     *
     *
     * @author 秦昊
     * Date: 2018/12/21 09:11
     * @param $file
     * @param $bankCode
     * @return array
     * @throws FileUploadException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function getResExcelData($bankCode, UploadedFile $file)
    {
        $bankInfo = $this->workTimeDao->findInfoByBankCode($bankCode);

        $filePath       = $this->getFilePath($file);
        $fileExt        = $file->getClientOriginalExtension();

        $titleMap       = array_flip(OriginExcelTitle::getNames());

        // 解析Excel数据
        $parseExcel     = new ParseExcel($filePath, $fileExt, $titleMap);
        $workData       = $parseExcel->getFirstSheetData();

//        dd($workData);
//        dd($parseExcel->getWorkDayList());

        $workTempData   = [];
        $onDutyTimeArr  = [];

        foreach ($workData as $workInfo)
        {
//            dump($workInfo);
            $dayTime    = $workInfo[OriginExcelTitle::DAY_TIME];
            $name       = $workInfo[OriginExcelTitle::NAME];
            $dutyTime   = $workInfo[OriginExcelTitle::DUTY_TIME];

            $resWorkInfo                                = [];
            $resWorkInfo[ResExcelTitle::NAME]           = $name;
            $resWorkInfo[ResExcelTitle::JOB_NUM]        = $workInfo[OriginExcelTitle::JOB_NUM];
            $resWorkInfo[ResExcelTitle::PROJECT_NAME]   = $workInfo[OriginExcelTitle::PROJECT_NAME];
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
            foreach ($workTempItem as $dayTime => $workInfo)
            {
//                dump($workInfo);
                $itemOnDutyTime = $workInfo[ResExcelTitle::ON_DUTY_TIME];
                $workInfo[ResExcelTitle::ON_DUTY_TIME]  = date('H:i:s', $itemOnDutyTime);
                $itemOffDutyTime= $workInfo[ResExcelTitle::OFF_DUTY_TIME];
                $workInfo[ResExcelTitle::OFF_DUTY_TIME] = date('H:i:s', $itemOffDutyTime);
                $week           = $workInfo[ResExcelTitle::WEEK];

                switch ($week)
                {
                    case Week::Saturday:
                    case Week::Sunday:
                        // 不计算迟到，只计算加班
                        $overTime = $bankInfo->getWeekendOverTime($itemOnDutyTime, $itemOffDutyTime);
                        $workInfo[ResExcelTitle::OVER_TIME] = $overTime;
                        $workInfo[ResExcelTitle::LATE_TIME] = '';
                        $workInfo[ResExcelTitle::STATUS]    = '';
                        break;
                    case Week::Monday:
                    case Week::Tuesday:
                    case Week::Wednesday:
                    case Week::Thursday:
                    case Week::Friday:
                        // 加班
                        $overTime = $bankInfo->getNormalOverTime($itemOffDutyTime);
                        $workInfo[ResExcelTitle::OVER_TIME] = $overTime;

                        // 计算迟到
                        $lateTime = $bankInfo->getLateTime($itemOnDutyTime);
                        $workInfo[ResExcelTitle::LATE_TIME] = $lateTime;

                        // 是否有异常
                        $status                             = $bankInfo->getStatus($itemOnDutyTime, $itemOffDutyTime);
                        $workInfo[ResExcelTitle::STATUS]    = $status;
                        break;
                }

                $resWorkData[]  = $workInfo;
            }
        }
//        dd($resWorkData);

        return $resWorkData;
    }



    /**
     * 获取Excel文件路径
     *
     * @author 秦昊
     * Date: 2018/12/17 15:17
     * @param $file
     * @return string
     * @throws FileUploadException
     */
    private function getFilePath($file)
    {
        if (!$file || !$file->isValid()) {
            throw new FileUploadException(600000);
        }

        $fileName = get_now() . '.' . $file->getClientOriginalExtension();

        $file->move(storage_path('files/excel/'), $fileName);

        $filePath = storage_path('files/excel/') . $fileName;

        return $filePath;
    }

}