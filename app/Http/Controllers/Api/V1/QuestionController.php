<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Modules\Question\Business\QuestionBusiness;
use Illuminate\Http\Request;

/**
 * APP端问答管理控制器
 *
 * @author 秦昊
 * Date: 2018/9/1 21:55
 */
class QuestionController extends Controller
{

    /**
     * 获取问答信息列表
     *
     * @author 秦昊
     * Date: 2018/9/1 21:44
     * @param Request $request
     * @param QuestionBusiness $questionBusiness
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function list(Request $request, QuestionBusiness $questionBusiness)
    {
        return $this->revert($questionBusiness->appList($request->all()));
    }

    /**
     * 获取问答信息详情
     *
     * @author 秦昊
     * Date: 2018/9/7 15:19
     * @param Request $request
     * @param QuestionBusiness $questionBusiness
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \App\Exceptions\AppException
     * @throws \App\Exceptions\FaqInfoException
     */
    public function show(Request $request, QuestionBusiness $questionBusiness)
    {
        $faqInfo = $questionBusiness->findOne($request->get('id'));

        $faqInfo->load([
            'Tags',
            'Answers' => function ($query){
                $query->orderBy('copy_count',       'DESC');
                $query->orderBy('last_update_time', 'DESC');
            }
        ]);

        return $this->revert($faqInfo);
    }

}