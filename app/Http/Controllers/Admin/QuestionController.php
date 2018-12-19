<?php

namespace App\Http\Controllers\Admin;

use App\Modules\Question\Business\QuestionBusiness;
use Illuminate\Http\Request;

/**
 * 问答信息控制器
 *
 * @author 秦昊
 * Date: 2018/9/1 09:51
 */
class QuestionController extends BaseController
{

    /**
     * 获取问答信息列表
     *
     * @author 秦昊
     * Date: 2018/8/31 09:53
     * @param Request $request
     * @param QuestionBusiness $questionBusiness
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function list(Request $request, QuestionBusiness $questionBusiness)
    {
        $selectColumns = ['id', 'title', 'last_edit_time'];

        $faqInfoList = $questionBusiness->list($request->all(), $selectColumns);

        return $this->revert($faqInfoList);
    }

    /**
     * 新增问答信息
     *
     * @author 秦昊
     * Date: 2018/8/31 16:34
     * @param Request $request
     * @param QuestionBusiness $questionBusiness
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function add(Request $request, QuestionBusiness $questionBusiness)
    {
        return $this->revert($questionBusiness->add($request->all()));
    }

    /**
     * 获取问答信息
     *
     * @author 秦昊
     * Date: 2018/9/7 15:27
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
            'Answers',
        ]);

        return $this->revert($faqInfo);
    }

    /**
     * 编辑问答信息
     *
     * @author 秦昊
     * Date: 2018/9/7 15:28
     * @param Request $request
     * @param QuestionBusiness $questionBusiness
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \App\Exceptions\AppException
     */
    public function update(Request $request, QuestionBusiness $questionBusiness)
    {
        $params = $request->all();

        $params['description'] = $params['description'] ?? '';

        return $this->revert($questionBusiness->update($request->get('id'), $params));
    }

    /**
     * 删除问答信息
     *
     * @author 秦昊
     * Date: 2018/9/7 15:28
     * @param Request $request
     * @param QuestionBusiness $questionBusiness
     * @return array|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \App\Exceptions\AppException
     */
    public function del(Request $request, QuestionBusiness $questionBusiness)
    {
        return $this->revert($questionBusiness->del($request->get('id')));
    }

}
