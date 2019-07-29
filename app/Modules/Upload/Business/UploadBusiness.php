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
     * @var HandleExcel
     */
    protected $handleExcel;

    /**
     * UploadBusiness constructor.
     * @param WorkTimeDao $workTimeDao
     * @param HandleExcel $handleExcel
     */
    public function __construct(
        WorkTimeDao $workTimeDao,
        HandleExcel $handleExcel
    ) {
        $this->workTimeDao  = $workTimeDao;
        $this->handleExcel  = $handleExcel;
    }

    /**
     * 解析文件，获取数据
     *
     * @author 秦昊
     * Date: 2018/12/21 09:41
     * @param UploadedFile $file
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

        $parseData      = $this->handleExcel->getParseData($file);

        $resExcelData   = $this->handleExcel->getResExcelData($bankInfo, $parseData);

        return $resExcelData;
    }

}