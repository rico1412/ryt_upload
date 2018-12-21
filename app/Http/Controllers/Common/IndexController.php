<?php

namespace App\Http\Controllers\Common;
use App\Modules\Upload\Business\UploadBusiness;


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
     * @param UploadBusiness $uploadBusiness
     * @return \Illuminate\View\View
     */
    public function index(UploadBusiness $uploadBusiness)
    {
        $projectList = $uploadBusiness->getProjectList();

        return view('upload.index', compact('projectList'));
    }

}