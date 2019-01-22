<?php

namespace App\Http\Controllers\Common;
use App\Modules\Upload\Business\ProjectBusiness;
use Illuminate\Http\Request;

/**
 *
 *
 * @author 秦昊
 * Date: 2018/12/19 15:41
 */
class IndexController extends BaseController
{
    /**
     * 展示首页
     *
     * @author 秦昊
     * Date: 2018/12/21 19:20
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('upload.upload', ['title' => '考勤文件转换系统']);
    }

    /**
     * 获取项目信息列表
     *
     * @author 秦昊
     * Date: 2018/12/27 13:58
     * @param ProjectBusiness $projectBusiness
     * @return array
     */
    public function getBankInfoList(ProjectBusiness $projectBusiness)
    {
        $bankInfoList   = $projectBusiness->getBankInfoList()->toArray();

        return [
            'code'      => 0,
            'msg'       => '',
            'count'     => count($bankInfoList),
            'data'      => $bankInfoList,
        ];
    }

    /**
     * 删除项目信息
     *
     * @author 秦昊
     * Date: 2018/12/27 13:58
     * @param Request $request
     * @param ProjectBusiness $projectBusiness
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function delBankInfo(Request $request, ProjectBusiness $projectBusiness)
    {
        $id = $request->get('id');

        return $this->revert($projectBusiness->delBankInfo($id));
    }

    /**
     * 编辑项目信息
     *
     * @author 秦昊
     * Date: 2018/12/28 12:52
     * @param Request $request
     * @param ProjectBusiness $projectBusiness
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \App\Exceptions\FaqInfoException
     */
    public function updateBankInfo(Request $request, ProjectBusiness $projectBusiness)
    {
        $id         = $request->get('id');

        $payload    = $request->only(['bank_code', 'project_name', 'on_duty_time_str', 'off_duty_time_str']);

        $res        = $projectBusiness->updateBankInfo($id, $payload);

        return $this->revert($res);
    }

}