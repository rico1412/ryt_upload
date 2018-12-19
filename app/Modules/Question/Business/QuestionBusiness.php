<?php

namespace App\Modules\Question\Business;

use App\Exceptions\AppException;
use App\Kernel\Base\BaseBusiness;
use App\Modules\Question\Dao\FaqAnswerDao;
use App\Modules\Question\Dao\FaqInfoDao;
use App\Modules\Question\Dao\FaqRelationInfoTagsDao;

/**
 * 问答业务处理
 *
 * @author 秦昊
 * Date: 2018/9/4 09:46
 */
class QuestionBusiness extends BaseBusiness
{
    protected $faqInfoDao               = null;
    protected $faqRelationInfoTagsDao   = null;
    protected $faqAnswerDao             = null;

    /**
     * QuestionBusiness constructor.
     * @param FaqInfoDao $faqInfoDao
     * @param FaqRelationInfoTagsDao $faqRelationInfoTagsDao
     * @param FaqAnswerDao $faqAnswerDao
     */
    public function __construct(
        FaqInfoDao              $faqInfoDao,
        FaqRelationInfoTagsDao  $faqRelationInfoTagsDao,
        FaqAnswerDao            $faqAnswerDao
    )
    {
        $this->faqInfoDao               = $faqInfoDao;
        $this->faqRelationInfoTagsDao   = $faqRelationInfoTagsDao;
        $this->faqAnswerDao             = $faqAnswerDao;
    }

    /**
     * 获取问答信息列表
     *
     * @author 秦昊
     * Date: 2018/8/31 10:50
     * @param array $params
     * @param array $columns
     * @return mixed
     */
    public function list(array $params, array $columns = [])
    {
        app('validator')->make($params, [
            'title'     => 'string|max:100',
            'page'      => 'int',
            'page_size' => 'int',
        ])->validate();

        $faqInfoList = $this->faqInfoDao->getListPage($params, $columns);

        return $faqInfoList;
    }

    /**
     * app端获取问答信息列表
     *
     * @author 秦昊
     * Date: 2018/9/1 21:45
     * @param array $params
     * @param array $columns
     * @return mixed
     */
    public function appList(array $params, array $columns = [])
    {
        app('validator')->make($params, [
            'search'    => 'string|max:100',
            'page'      => 'int',
            'page_size' => 'int',
        ])->validate();

        $faqInfoList = $this->faqInfoDao->getAppListPage($params, $columns);

        $faqInfoList->load(['Tags'=>function($query){
            $query->select(['name']);
        }]);

        return $faqInfoList;
    }

    /**
     * 新增问答信息
     *
     * @author 秦昊
     * Date: 2018/8/31 16:34
     * @param array $params
     * @return mixed
     */
    public function add(array $params)
    {
        $params = array_where($params, function($value){
            return (!is_string($value) && $value !== null) || strlen($value) > 0;
        });

        app('validator')->make($params, [
            'title'             => 'required|string|max:100|unique:faq_info,title,0,id,deleted_at,NULL',
            'description'       => 'string|max:200',
            'tags'              => 'array',
            'answers'           => 'required|array',
        ], [
            'title.unique'      => '问答标题已存在，请勿重复添加',
        ])->validate();

        $params['last_edit_time'] = get_now();

        $faqInfo = app('db')->transaction(function () use($params){
            // 1. 存储问答信息
            $faqInfo = $this->faqInfoDao->store($params);

            // 2. 获取问答信息的id
            $faqInfoId = $faqInfo->id;

            // 3. 同步标签关联关系，非必传
            $this->faqRelationInfoTagsDao->syncInfoRelations($faqInfoId, array_get($params, 'tags', []));

            // 4. 同步答案，必传
            $this->faqAnswerDao->syncAnswers($faqInfoId, $params['answers']);

            // 5. 冗余数据
            $faqInfo = $this->syncData($faqInfoId);

            return $faqInfo;
        }, 2);

        return $faqInfo;
    }

    /**
     * 获取某一条问答信息
     *
     * @author 秦昊
     * Date: 2018/9/7 15:18
     * @param $id
     * @param array $columns
     * @return mixed
     * @throws AppException
     * @throws \App\Exceptions\FaqInfoException
     */
    public function findOne($id, array $columns = [])
    {
        if (!$id) {
            throw new AppException(100003);
        }

        $this->faqInfoDao->findOrFail($id, $columns);

        $faqInfo = $this->syncData($id);

        return $faqInfo;
    }

    /**
     * 编辑问答信息
     *
     * @author 秦昊
     * Date: 2018/9/7 15:18
     * @param $id
     * @param array $params
     * @return mixed
     * @throws AppException
     */
    public function update($id, array $params)
    {
        if (!$id) {
            throw new AppException(100003);
        }

        $params = array_where($params, function($value){
            return (!is_string($value) && $value !== null) || strlen($value) >= 0;
        });

        app('validator')->make($params, [
            'title'             => 'required|string|max:100|unique:faq_info,title,'.$id.',id,deleted_at,NULL',
            'description'       => 'string|max:200',
            'tags'              => 'array',
            'answers'           => 'required|array',
        ], [
            'title.unique'      => '问答标题已存在，请勿重复添加',
        ])->validate();

        $params['last_edit_time'] = get_now();

        $faqInfo = app('db')->transaction(function () use($id, $params){
            // 1. 更新问答信息
            $params['last_update_time'] = get_now();
            $this->faqInfoDao->update($id, $params);

            // 3. 同步标签关联关系，非必传
            $this->faqRelationInfoTagsDao->syncInfoRelations($id, array_get($params, 'tags', []));

            // 4. 同步答案，必传
            $this->faqAnswerDao->syncAnswers($id, $params['answers']);

            // 5. 冗余数据
            $faqInfo = $this->syncData($id);

            return $faqInfo;
        }, 2);

        return $faqInfo;
    }

    /**
     * 冗余数据
     *
     * @author 秦昊
     * Date: 2018/8/31 16:40
     * @param $faqInfoId
     * @return mixed
     * @throws \App\Exceptions\FaqInfoException
     */
    public function syncData($faqInfoId)
    {
        //获取问题内容
        $faqInfo = $this->faqInfoDao->findOrFail($faqInfoId, ['*']);
        
        // 获取最佳答案
        $bestAnswer = $this->faqAnswerDao->getBestAnswer($faqInfoId);

        // 获取tag列表
        $tags = $faqInfo->Tags->toArray();

        // 构建search字段
        $buildSearchColumn = function($title, $content, array $tags) {
            $searchArr = array();
            
            $searchArr[] = $title;
            
            foreach ($tags as $tag) {
                $searchArr[] = $tag['name'];
            }
            
            $searchArr[] = $content;

            return implode('|', $searchArr);
        };

        $updateInfo = [];
        $updateInfo['best_answer_id']             = $bestAnswer->id ?? 0;
        $updateInfo['best_answer_copy_count']     = $bestAnswer->copy_count ?? 0;
        $updateInfo['best_answer_content']        = $bestAnswer->content ?? '';
        // 获取所有答案复制的总次数
        $updateInfo['copy_total_count']           = $this->faqAnswerDao->getCopyTotalCount($faqInfoId);
        // 构建搜索字段
        $updateInfo['search']                     = $buildSearchColumn($faqInfo->title, $bestAnswer->content, $tags);

        return $this->faqInfoDao->update($faqInfoId, $updateInfo);
    }

    /**
     * 删除问答信息
     *
     * @author 秦昊
     * Date: 2018/9/7 15:18
     * @param $id
     * @return mixed
     * @throws AppException
     */
    public function del($id)
    {
        if (!$id) {
            throw new AppException(100003);
        }

        $faqInfo = app('db')->transaction(function () use($id){
            // 删除自身信息
            $faqInfo = $this->faqInfoDao->destroy($id);

            // 删除与标签的关联关系
            $this->faqRelationInfoTagsDao->deleteByFaqInfoId($id);

            // 删除对应的问答答案
            $this->faqAnswerDao->deleteByPid($id);

            return $faqInfo;
        }, 2);

        return $faqInfo;
    }

    /**
     * 增加答案复制次数
     *
     * @author 秦昊
     * Date: 2018/9/7 15:21
     * @param $id
     * @return mixed
     * @throws AppException
     */
    public function addCopyCount($id)
    {
        if (!$id) {
            throw new AppException(100003);
        }

        $faqAnswer = app('db')->transaction(function () use($id){
            // 根据传过来的id插件问答答案详情是否存在
            $faqAnswer = $this->faqAnswerDao->findOrFail($id);
            // 使copy_count字段自增1
            $faqAnswer->increment('copy_count');

            // 根据答案获取对应问答信息
            $faqInfo = $faqAnswer->FaqInfo;
            // 更新问答信息的冗余数据
            $this->syncData($faqInfo->id);
            // 去掉问答答案中关联的问答信息，因为它并不准确
            unset($faqAnswer->FaqInfo);

            return $faqAnswer;
        }, 2);

        return $faqAnswer;
    }

}