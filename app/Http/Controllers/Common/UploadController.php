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
     * 导出处理好的Excel表
     *
     * @author 秦昊
     * Date: 2018/12/20 14:00
     * @param Request $request
     * @param UploadBusiness $uploadBusiness
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \App\Exceptions\FileUploadException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function export(Request $request, UploadBusiness $uploadBusiness)
    {
        validator($request->all(), [
            'excel' => 'required',
        ], [
            'excel.required' => 'Excel文件上传错误',
        ])->validate();

        $excel      = $request->file('excel');
        $fileName   = get_file_name($excel) . '_res';

        $data       = $uploadBusiness->getResData($excel);

        $headList   = array_values(ResExcelTitle::getNames());

        $filePath   = config('domain.common') . csv_export($data, $headList, $fileName);

        return $this->revert(compact('filePath'));
    }

}