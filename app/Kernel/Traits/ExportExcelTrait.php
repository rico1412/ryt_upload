<?php

namespace App\Kernel\Traits;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

/**
 * Excel导出
 */
trait ExportExcelTrait
{
    /**
     * Excel导出
     *
     * @param array $data 导出数据，格式['A1' => 'XXXX公司报表', 'B1' => '序号', 'A2:B2' => 'A2:B2单元格合并']
     * @param string $fileName 导出文件名称
     * @param bool $multiSheet 是否多个sheet
     * @param array $options 操作选项，例如：
     *                          bool  print      设置打印格式
     *                          string freezePane  锁定行数，例如表头为第一行，则锁定表头输入A2
     *                          array  setARGB    设置背景色，例如['A1', 'C1']
     *                          array  setWidth    设置宽度，例如['A' => 30, 'C' => 20]
     *                          bool  setBorder  设置单元格边框
     *                          array  mergeCells  设置合并单元格，例如['A1:J1' => 'A1:J1']
     *                          array  formula    设置公式，例如['F2' => '=IF(D2>0,E42/D2,0)']
     *                          array  format      设置格式，整列设置，例如['A' => 'General']
     *                          array  alignCenter 设置居中样式，例如['A1', 'A2']
     *                          array  bold        设置加粗样式，例如['A1', 'A2']
     *                          string savePath    保存路径，设置后则文件保存到服务器，不通过浏览器下载
     * @return bool
     * @throws \Exception
     */
    public function exportExcel(array $data, string $fileName = '', array $options = [], $multiSheet = false): bool
    {
        try
        {
            if (empty($data)) return false;
            
            $objSpreadsheet = new Spreadsheet();
    
            $datas = $data;
            
            // 不是多个sheet转成多个sheet的二维格式
            if (! $multiSheet) {
                $datas = [$data];
            }
            
            $sheetIndex = 0;
            
            foreach ($datas as $sheetName => $sheetData) {
                
                if($sheetIndex) $objSpreadsheet->createSheet($sheetIndex);
                
                /* 设置Excel Sheet */
                $activeSheet = $objSpreadsheet->setActiveSheetIndex($sheetIndex);
                
                if ($multiSheet) $activeSheet->setTitle($sheetName);
                
                // 已经合并了的单元格
                $mergedCells = [];
                
                /* 行数据处理 */
                foreach ($sheetData as $row => $rowData)
                {
                    $columnIndex = 0;

                    foreach ($rowData as $sKey => $sItem)
                    {
                        // 合并单元格 'A1:E1'
                        if (false !== ($key = strstr($sKey, ':', true)))
                        {
                            if (! array_has($mergedCells, $sKey))
                            {
                                $mergedCells[] = $sKey;
            
                                $activeSheet->mergeCells($sKey);
                            }
        
                            $sKey = $key;
                        }

                        if (is_numeric($sKey) || !preg_match('/^[A-Z]+[\d]+$/', $sKey))
                        {
                            $sKey       = $columnIndex;
                            $pCoordinate= $this->getCellKey($row, $sKey);
                            $activeSheet->setCellValueExplicit($pCoordinate, $sItem, DataType::TYPE_STRING);
                        } else {
                            $pCoordinate= $sKey;
                            $activeSheet->setCellValueExplicit($pCoordinate, $sItem, DataType::TYPE_STRING);
                        }

                        // 默认横竖居中
                        $activeSheet->getStyle($pCoordinate)->applyFromArray([
                            'alignment' => [
                                'horizontal'=> Alignment::HORIZONTAL_CENTER,
                                'vertical'  => Alignment::VERTICAL_CENTER,
                            ]
                        ]);

                        ++$columnIndex;
                    }
                }
                
                unset($mergedCells);
                
                ++$sheetIndex;
            }
            
            unset($datas);
            
            /* 设置锁定行 */
            
            if (isset($options['freezePane']) && !empty($options['freezePane'])) {
                
                $activeSheet->freezePane($options['freezePane']);
                
                unset($options['freezePane']);
            }
            
            /* 设置宽度 */
            
            if (isset($options['setWidth']) && !empty($options['setWidth'])) {
                
                foreach ($options['setWidth'] as $swKey => $swItem) {
                    
                    $activeSheet->getColumnDimension($swKey)->setWidth($swItem);
                    
                }
                
                unset($options['setWidth']);
                
            }
            
            /* 设置背景色 */
            
            if (isset($options['setARGB']) && !empty($options['setARGB'])) {
                
                foreach ($options['setARGB'] as $cellIndex => $color) {
                    
                    $activeSheet->getStyle($cellIndex)
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($color ?: Color::COLOR_YELLOW);
                    
                }
                
                unset($options['setARGB']);
                
            }
            
            /* 设置居中 */
            
            if (isset($options['alignCenter']) && !empty($options['alignCenter'])) {
    
                $styleArray = [
                    'alignment' => [
            
                        'horizontal' => array_get($options, 'alignStyle.horizontal', Alignment::HORIZONTAL_CENTER),
            
                        'vertical'  => array_get($options, 'alignStyle.vertical', Alignment::VERTICAL_CENTER),
                    ],
                ];
                
                foreach ($options['alignCenter'] as $acItem) {
                    
                    $activeSheet->getStyle($acItem)->applyFromArray($styleArray);
                }
                
                unset($options['alignCenter']);
                
            }
            
            /* 设置加粗 */
            
            if (isset($options['bold']) && !empty($options['bold'])) {
                
                foreach ($options['bold'] as $bItem) {
                    
                    $activeSheet->getStyle($bItem)->getFont()->setBold(true);
                    
                }
                
                unset($options['bold']);
                
            }
            
            /* 设置单元格边框，整个表格设置即可，必须在数据填充后才可以获取到最大行列 */
            
            if (isset($options['setBorder']) && $options['setBorder']) {
                
                $border = [
                    
                    'borders' => [
                        
                        'allBorders' => [
                            
                            'borderStyle' => Border::BORDER_THIN, // 设置border样式
                            
                            'color' => ['argb' => 'FF000000'], // 设置border颜色
                        
                        ],
                    
                    ],
                
                ];
                
                $setBorder = 'A1:' . $activeSheet->getHighestColumn() . $activeSheet->getHighestRow();
                
                $activeSheet->getStyle($setBorder)->applyFromArray($border);
                
                unset($options['setBorder']);
                
            }
            
            if (!isset($options['savePath'])) {
    
                if ($fileName) {
                    $fileName = (false === strpos($fileName, '.')) ? $fileName . '.xlsx' : $fileName;
                } else {
                    $fileName = date('YmdHis') . '.xlsx';
                }
                
                /* 直接导出Excel，无需保存到本地，输出07Excel文件 */
                
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                
                header('Content-Disposition:attachment;filename=' . $fileName);
                
                header('Cache-Control: max-age=0');//禁止缓存
                
                $savePath = 'php://output';
                
            } else {
                
                $savePath = $options['savePath'];
            }
            
            ob_start();
            
            $objWriter = IOFactory::createWriter($objSpreadsheet, 'Xlsx');
            
            $objWriter->save($savePath);
            
            /* 释放内存 */
            
            $objSpreadsheet->disconnectWorksheets();
            
            unset($objSpreadsheet);
            
            return true;
            
        } catch (\Exception $exception)
        {
            info($exception->getMessage());
            
            if(config('app.env') !== 'production') {
                throw $exception;
            }
        }
    }

    /**
     * 将行列的数字转换成Excel的单元格号码
     *
     * @param $row
     * @param $column
     * @return string
     * @author 秦昊
     * Date: 2019-08-14 11:57
     */
    private function getCellKey($row, $column)
    {
        $count = floor($column / 26) + 1;

        $columnStr = '';

        for ($i = 0; $i < $count; $i++, $column -= 26)
        {
            $columnStr .= chr($column % 26 + 65);
        }

        $row++;

        return "{$columnStr}{$row}";
    }
}

