<?php

namespace App\Http\Controllers\Common;

use App\Modules\Upload\Business\UploadBusiness;
use App\Modules\Upload\Constant\ResExcelTitle;
use Illuminate\Http\Request;

/**
 *
 *
 * @author 秦昊
 * Date: 2018/12/19 15:45
 */
class UploadController extends BaseController
{
    /**
     *
     *
     * @author 秦昊
     * Date: 2018/12/20 14:00
     * @param Request $request
     * @param UploadBusiness $uploadBusiness
     * @throws \App\Exceptions\FileUploadException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function upload(Request $request, UploadBusiness $uploadBusiness)
    {
        $bankCode   = $request->get('bank_code');
        $excel      = $request->file('excel');

        $data = $uploadBusiness->getResExcel($bankCode, $excel);

        $headList   = array_values(ResExcelTitle::getNames());

        csv_export($data, $headList, 'test');
    }

}