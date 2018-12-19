<?php


namespace App\Http\Controllers\Admin;

use App\Modules\Question\Business\FaqTagBusiness;
use Illuminate\Http\Request;

/**
 * 问答标签控制器
 *
 * @author 秦昊
 * Date: 2018/9/1 09:51
 */
class FaqTagController extends BaseController
{

    /**
     * 获取问答标签列表
     *
     * @author 秦昊
     * Date: 2018/9/1 12:18
     * @param Request $request
     * @param FaqTagBusiness $faqTagBusiness
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function list(Request $request, FaqTagBusiness $faqTagBusiness)
    {
        return $this->revert($faqTagBusiness->list($request->all()));
    }

    /**
     * 获取问答标签分页列表
     *
     * @author 秦昊
     * Date: 2018/9/11 14:56
     * @param Request $request
     * @param FaqTagBusiness $faqTagBusiness
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function pageList(Request $request, FaqTagBusiness $faqTagBusiness)
    {
        return $this->revert($faqTagBusiness->pageList($request->all()));
    }

    /**
     * 添加问题标签
     *
     * @author 秦昊
     * Date: 2018/9/1 12:53
     * @param Request $request
     * @param FaqTagBusiness $faqTagBusiness
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function add(Request $request, FaqTagBusiness $faqTagBusiness)
    {
        return $this->revert($faqTagBusiness->add($request->all()));
    }

    /**
     * 删除问题标签及对应的关联关系
     *
     * @author 秦昊
     * Date: 2018/9/7 15:29
     * @param Request $request
     * @param FaqTagBusiness $faqTagBusiness
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \App\Exceptions\AppException
     */
    public function del(Request $request, FaqTagBusiness $faqTagBusiness)
    {
        return $this->revert($faqTagBusiness->del($request->get('id')));
    }

}