<?php

namespace App\Modules\Upload\Tools;

use App\Exceptions\FileUploadException;
use App\Modules\Upload\Constant\OriginExcelTitle;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * 解析Excel文件
 *
 * @author 秦昊
 * Date: 2018/12/13 19:46
 */
class ParseExcel
{
    /*
     * 解析后的数据结构：
     *
     * 表头结构：
     * [
     *      列位置 => 表头1,
     *      列位置 => 表头2,
     *      列位置 => 表头n,
     * ]
     *
     * 表结构：
     * [
     *      行位置 => [
     *          表头1 => 值1,
     *          表头2 => 值2,
     *          表头n => 值n,
     *      ],
     * ]
     */

    /**
     * @var array
     */
    protected $titleMap;

    /**
     * @var array
     */
    protected $headMap;

    /**
     * @var Xlsx
     */
    protected $excelReader;

    /**
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    protected $excel;

    /**
     * @var array
     */
    protected $workDayList;

    /**
     * ParseExcel constructor.
     * @param $path
     * @param $fileExt
     * @param array $titleMap
     * @throws FileUploadException
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function __construct($path, $fileExt, array $titleMap)
    {
        $this->titleMap = $titleMap;

        $fileExt = strtolower($fileExt);

        if ($fileExt === 'xlsx')
        {
            $this->excelReader  = new Xlsx();
        } else if ($fileExt === 'xls')
        {
            $this->excelReader  = new Xls();
        } else {
            throw new FileUploadException(600004);
        }

        $this->excel = $this->excelReader->load($path);

        if (empty($this->excel))
        {
            throw new FileUploadException(600000);
        }

        $this->workDayList = [];
    }

    /**
     * 获取Excel中所有Sheet的数据
     *
     * @author 秦昊
     * Date: 2018/12/18 17:19
     * @return array
     * @throws FileUploadException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function getAllData()
    {
        $linkExcel = [];

        foreach ($this->excel->getAllSheets() as $key => $sheet)
        {
            // 总行数
            if($sheet->getHighestDataRow() < 1) continue;

            //处理表格的业务
            $linkSheet = $this->handleSheet($sheet);

            $linkExcel[$key + 1] = $linkSheet;
        }

        if (empty($linkExcel)) throw new FileUploadException(600002);

        return $linkExcel;
    }

    /**
     * 获取第一个Sheet的数据
     *
     * @author 秦昊
     * Date: 2018/12/17 19:44
     * @return array
     * @throws FileUploadException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function getFirstSheetData() : array
    {
        $sheet = $this->excel->getSheet($this->excel->getFirstSheetIndex());

        // 总行数
        if($sheet->getHighestDataRow() < 1) return null;

        //处理表格的业务
        return $this->handleSheet($sheet);
    }

    /**
     * 处理 sheet
     *
     * @author 秦昊
     * Date: 2018/12/22 17:22
     * @param Worksheet $sheet
     * @return array
     * @throws FileUploadException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function handleSheet(Worksheet $sheet)
    {
        // 获取表头信息
        foreach ($sheet->getRowIterator() as $rk => $rv)
        {
            if (empty($rv)) continue;

            foreach ($rv->getCellIterator() as $key => $value)
            {
                $cellValue = $value->getValue();

                if (empty($cellValue)) continue;

                if (is_string($cellValue) && $headValue = array_get($this->titleMap, $cellValue))
                {
                    $this->headMap[$key] = $headValue;
                } else {
                    throw new FileUploadException(600003, ['message' => '表头：'. $value->getValue() .' 不符合规范']);
                }
            }

            break;
        }

//        dd($this->headMap);

        if (empty($this->headMap)) throw new FileUploadException(600003);

        //整理数据
        $linkSheet = array();

        // 总行数
        $rowCount = $sheet->getHighestDataRow();

        for ($row = 2; $row <= $rowCount; $row++)
        {
            $linkRow = array();

            foreach($this->headMap as $mk => $mv)
            {
                if ($cell = $sheet->getCell($mk . $row))
                {
                    $cellValue = $cell->getValue();

                    if ($cellValue instanceof RichText)
                    {
                        $cellValue = $cellValue->getPlainText();
                    }

                    $linkRow[$mv] = $cellValue;
                }
            }

            // 去除空数据
            $linkRow = array_filter($linkRow, function($data)
            {
                return filled($data);
            });

            if(empty($linkRow)) continue;

            $linkSheet[$row] = $linkRow;
        }

        if (empty($linkSheet)) throw new FileUploadException(600002);

        return $linkSheet;
    }

    /**
     * 获取表头
     *
     * @author 秦昊
     * Date: 2018/12/13 19:59
     * @return mixed
     */
    public function getHeadMap()
    {
        return $this->headMap;
    }

    public function getWorkDayList()
    {
        return $this->workDayList;
    }

}