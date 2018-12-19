<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Modules\Question\Business\QuestionBusiness;
use Illuminate\Http\Request;

/**
 *
 *
 * @author 秦昊
 * Date: 2018/9/7 15:31
 */
class FaqAnswerController extends BaseController
{

    /**
     * 增加答案复制次数
     *
     * @author 秦昊
     * Date: 2018/9/7 15:26
     * @param Request $request
     * @param QuestionBusiness $questionBusiness
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \App\Exceptions\AppException
     */
    public function addCopyCount(Request $request, QuestionBusiness $questionBusiness)
    {
        return $this->revert($questionBusiness->addCopyCount($request->get('id')));
    }

}