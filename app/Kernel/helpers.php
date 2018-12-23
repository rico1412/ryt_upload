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

if (!function_exists('time_to_second')) {

    /**
     * 时分秒转换为秒数
     *
     * @author 秦昊
     * Date: 2018/12/22 07:53
     * @param $time
     * @return float|int
     */
    function time_to_second($time)
    {
        if ($time && ($timeArr = explode(':', $time))
            && ($arrLength = count($timeArr)) >= 2)
        {
            $h = (int)$timeArr[0];
            $m = (int)$timeArr[1];
            $s = 0;

            if ($arrLength == 3)
            {
                $s = (int)$timeArr[2];
            }

            return $h * 3600 + $m * 60 + $s;
        }

        return 0;
    }
}

if (!function_exists('get_page_size')) {

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

if (!function_exists('format_content')) {

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

if (!function_exists('get_week')) {

    /**
     * 日期转换为星期
     *
     * @author 秦昊
     * Date: 2018/12/20 09:38
     * @param $date
     * @return mixed
     */
    function get_week($date)
    {
        //强制转换日期格式
        $date_str=date('Y-m-d',strtotime($date));

        //封装成数组
        $arr=explode("-", $date_str);

        //参数赋值
        //年
        $year=$arr[0];

        //月，输出2位整型，不够2位右对齐
        $month=sprintf('%02d',$arr[1]);

        //日，输出2位整型，不够2位右对齐
        $day=sprintf('%02d',$arr[2]);

        //时分秒默认赋值为0；
        $hour = $minute = $second = 0;

        //转换成时间戳
        $strap = mktime($hour,$minute,$second,$month,$day,$year);

        //获取数字型星期几
        $number_wk=date("w",$strap);

        //自定义星期数组
        $weekArr=array("星期日","星期一","星期二","星期三","星期四","星期五","星期六");

        //获取数字对应的星期
        return $weekArr[$number_wk];
    }
}

if (!function_exists('csv_export'))
{
    /**
     * 导出excel(csv)
     *
     * @author 秦昊
     * Date: 2018/11/30 17:15
     * @param array $data       导出数据
     * @param array $headList   第一行,列名
     * @param $fileName
     */
    function csv_export(array $data = [], array $headList = [], $fileName)
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'.csv"');
        header('Cache-Control: max-age=0');

        //打开PHP文件句柄,php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');

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
    }
}


