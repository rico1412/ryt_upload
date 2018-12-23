<?php

namespace App\Modules\Upload\Business;

use App\Exceptions\FileUploadException;
use App\Kernel\Base\BaseBusiness;
use App\Modules\Upload\Dao\WorkTimeDao;
use App\Modules\Upload\Tools\HandleExcel;
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
     * 解析文件，获取数据
     *
     * @author 秦昊
     * Date: 2018/12/21 09:41
     * @param $file
     * @return array
     * @throws FileUploadException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function getResData(UploadedFile $file)
    {
        $fileName   = get_file_name($file);

        if (empty($bankInfo = $this->workTimeDao->findInfoByBankCode($fileName)))
        {
            throw new FileUploadException(600005);
        }

        $parseData      = HandleExcel::getParseData($file);

        $resExcelData   = HandleExcel::getResExcelData($bankInfo, $parseData);

        return $resExcelData;
    }

}