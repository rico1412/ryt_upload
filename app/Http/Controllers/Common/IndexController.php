<?php

namespace App\Http\Controllers\Common;

/**
 *
 *
 * @author 秦昊
 * Date: 2018/12/19 15:41
 */
class IndexController extends BaseController
{
    /**
     *
     *
     * @author 秦昊
     * Date: 2018/12/21 19:20
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('upload.upload');
    }

}