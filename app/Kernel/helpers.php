<?php

if (!function_exists('get_file_path'))
{
    /**
     * 获取Excel文件路径
     *
     * @author 秦昊
     * Date: 2018/12/17 15:17
     * @param $file
     * @return string
     * @throws \App\Exceptions\FileUploadException
     */
    function get_file_path(\Illuminate\Http\UploadedFile $file)
    {
        if (!$file || !$file->isValid()) throw new \App\Exceptions\FileUploadException(600000);

        $fileName = get_now() . '.' . $file->getClientOriginalExtension();

        $file->move(storage_path('files/excel/'), $fileName);

        $filePath = storage_path('files/excel/') . $fileName;

        return $filePath;
    }
}

if (!function_exists('get_file_name'))
{
    /**
     * 获取原始文件名
     *
     * @author 秦昊
     * Date: 2018/12/23 11:55
     * @param \Illuminate\Http\UploadedFile $file
     * @return mixed|null|string
     * @throws \App\Exceptions\FileUploadException
     */
    function get_file_name(\Illuminate\Http\UploadedFile $file)
    {
        if (!$file || !$file->isValid()) throw new \App\Exceptions\FileUploadException(600000);

        $fileName   = $file->getClientOriginalName();

        if (strpos($fileName, '.'))
        {
            $fileNameArr    = explode('.', $fileName);

            $fileName       = array_shift($fileNameArr);
        }

        return $fileName;
    }
}

if (!function_exists('time_to_second'))
{
    /**
     * 时间转换为秒数
     *
     * @author 秦昊
     * Date: 2018/12/22 07:53
     * @param $time string
     * @return string
     */
    function time_to_second(string $time)
    {
        return \Carbon\Carbon::createFromTimeString($time)->secondsSinceMidnight();
    }
}

if (!function_exists('second_to_time'))
{
    /**
     * 秒数转换为时分
     *
     * @author 秦昊
     * Date: 2018/12/22 07:53
     * @param $second int
     * @return string
     */
    function second_to_time(int $second)
    {
        return \Carbon\Carbon::createFromTime(0, 0)
            ->addSeconds($second)->format('H:i');
    }
}

if (!function_exists('get_page_size'))
{
    /**
     * 获取每页默认记录数
     * User: lizhenhai
     * Date: 2018/7/20 0020
     * @param $param
     * @return int
     */
    function get_page_size($param)
    {
        $pageSize = array_get($param, 'page_size');

        return (int)$pageSize > 0 ? $pageSize : 10;
    }
}

if (!function_exists('format_content'))
{
    /**
     * 格式化content
     *
     * @author 秦昊
     * Date: 2018/9/5 15:56
     * @param $content
     * @return mixed
     */
    function format_content($content)
    {
        return str_replace('；', PHP_EOL, $content);
    }
}

if (!function_exists('get_week'))
{
    /**
     * 日期转换为星期
     *
     * @author 秦昊
     * Date: 2018/12/20 09:38
     * @param $date
     * @param $inFormat
     * @return mixed
     */
    function get_week($date, $inFormat = 'Ymd')
    {
        $arr = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];

        $index = \Carbon\Carbon::createFromFormat($inFormat, $date)->dayOfWeek;

        return array_get($arr, $index, '');
    }
}

if(!function_exists('check_date_is_valid'))
{
    /**
     * 校验日期格式是否正确
     *
     * @param string $date 日期
     * @param array $formats 需要检验的格式数组
     * @return boolean
     */
    function check_date_is_valid($date, array $formats = [])
    {
        $unixTime = strtotime($date);

        if (!$unixTime) { //strtotime转换不对，日期格式显然不对。
            return false;
        }

        $formatsArr = array('Y-m-d', 'Y/m/d','Y-m-d H:i:s');
        $formatsArr = array_merge($formatsArr, $formats);

        //校验日期的有效性，只要满足其中一个格式就OK
        foreach ($formatsArr as $format)
        {
            if (date($format, $unixTime) === $date)
            {
                return true;
            }
        }

        return false;
    }
}

if(!function_exists('transfer_excel_date'))
{
    /**
     * 转换 excel 日期
     * @param $excelDate
     * @param string $format
     * @param string $default
     * @return string
     */
    function transfer_excel_date($excelDate, $format = 'YmdHis', $default = '0')
    {
        if(check_date_is_valid($excelDate, [$format]))
        {
            return date($format, strtotime($excelDate));
        }

        try
        {
            $tmp = (int) (($excelDate - 25569) * 3600 * 24);

        } catch (Exception $exception)
        {
            return $excelDate;
        }

        return gmdate($format, $tmp);
    }
}

if (!function_exists('csv_export'))
{
    /**
     * 导出excel(csv)
     *
     * @author 秦昊
     * Date: 2018/11/30 17:15
     * @param array $data 导出数据
     * @param array $headList 第一行,列名
     * @param $fileName
     * @return string
     */
    function csv_export(array $data = [], array $headList = [], $fileName = '')
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'.csv"');
        header('Cache-Control: max-age=0');

        //打开PHP文件句柄,php://output 表示直接输出到浏览器
//        $fp = fopen('php://output', 'a');
        @unlink($fileName.'.csv');
        $fp = fopen($fileName.'.csv', 'a');

        //输出Excel列名信息
        foreach ($headList as $key => $value)
        {
            //CSV的Excel支持GBK编码，一定要转换，否则乱码
            $headList[$key] = iconv('utf-8', 'gbk', $value);
        }

        //将数据通过fputcsv写到文件句柄
        fputcsv($fp, $headList);

        //计数器
        $num = 0;

        //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 100000;

        //逐行取出数据，不浪费内存
        $count = count($data);
        for ($i = 0; $i < $count; $i++)
        {
            $num++;

            //刷新一下输出buffer，防止由于数据过多造成问题
            if ($limit == $num) {
                ob_flush();
                flush();
                $num = 0;
            }

            $row = $data[$i];
            foreach ($row as $key => $value)
            {
                $row[$key] = iconv('utf-8', 'gbk', $value);
            }

            fputcsv($fp, $row);
        }

        fclose($fp);

        $filePath = $fileName.'.csv';

        return $filePath;
    }
}


