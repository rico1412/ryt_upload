<?php


namespace App\Modules\Question\Dao;

use App\Modules\Question\Model\FaqAnswer;
use Illuminate\Database\Eloquent\Model;

/**
 * 问答答案数据处理
 *
 * @author 秦昊
 * Date: 2018/8/31 14:22
 */
class FaqAnswerDao extends BaseDao
{
    protected $selectColumns = [
        'id',
        'faq_info_id',
        'content',
        'copy_count',
        'add_time',
        'last_update_time',
    ];

    /**
     * Get model.
     *
     * @return Model
     */
    protected function getModel(): Model
    {
        return app(FaqAnswer::class);
    }

    /**
     * 获取某问答信息下的最佳答案
     *
     * @author 秦昊
     * Date: 2018/8/31 15:18
     * @param $pid
     * @return mixed
     */
    public function getBestAnswer($pid)
    {
        return $this->doQuery(['faq_info_id'=>$pid])
                    ->orderBy('copy_count', 'DESC')
                    ->first();
    }

    /**
     * 获取某问答信息下所有答案复制的总次数
     *
     * @author 秦昊
     * Date: 2018/8/31 15:23
     * @param $faqInfoId
     * @return mixed
     */
    public function getCopyTotalCount($faqInfoId)
    {
        return $this->doQuery(['faq_info_id'=>$faqInfoId])->sum('copy_count');
    }

    /**
     * 同步问答答案
     *
     * @author 秦昊
     * Date: 2018/9/11 11:16
     * @param $faqInfoId
     * @param array $newAnswers
     * @throws \App\Exceptions\FaqInfoException
     */
    public function syncAnswers($faqInfoId, array $newAnswers)
    {
        $idArr = array();
        foreach($newAnswers as $ak=>$av){
            if(!isset($av['id']) && !empty($av['content'])){
                // 新增数据
                $addParams = [
                    'faq_info_id'       => $faqInfoId,
                    'content'           => $av['content'],
                ];
                $addAnswer = $this->store($addParams);
                $idArr[] = $addAnswer->id;
            }else{
                // 修改数据
                $updateParams = [
                    'content'           => $av['content'],
                ];
                $this->update($av['id'], $updateParams);
                $idArr[] = $av['id'];
            }
        }

        // 查询当前问答答案
        $currentAnswerList = $this->getListByPid($faqInfoId, ['id', 'content']);
        // 当前问答答案数组
        $currentIds = array_pluck($currentAnswerList->toArray(), 'id');
        // 获取要删除的答案ids
        $delIdArr   = array_diff($currentIds, $idArr);
        // 删除答案
        $this->deleteSomeAnswersById($faqInfoId, $delIdArr);
    }

    /**
     * 删除某问答信息下的某些答案
     *
     * @author 秦昊
     * Date: 2018/8/31 14:57
     * @param $faqInfoId
     * @param array $ids
     * @return mixed
     */
    public function deleteSomeAnswersById($faqInfoId, array $ids)
    {
        if (!empty($ids)){// 必须判空，不然会删除所有
            return $this->doQuery([
                            'faq_info_id'=>$faqInfoId,
                            'ids'=>$ids
                        ])->delete();
        }

        return null;
    }

    /**
     * 删除某问答信息下的所有答案
     *
     * @author 秦昊
     * Date: 2018/8/31 17:48
     * @param $faqInfoId
     * @return mixed
     */
    public function deleteByPid($faqInfoId)
    {
        return $this->doQuery(['faq_info_id'=>$faqInfoId])->delete();
    }

    /**
     * 根据问答信息id获取对应的答案列表
     *
     * @author 秦昊
     * Date: 2018/8/31 14:50
     * @param $faqInfoId
     * @param array $columns
     * @return mixed
     */
    public function getListByPid($faqInfoId, array $columns = [])
    {
        return $this->doQuery(['faq_info_id'=>$faqInfoId], $columns)->get();
    }

    /**
     * 构建筛选条件
     *
     * @param array $param
     * @param array $selectColumn
     * @return mixed
     */
    protected function doQuery(array $param = [], array $selectColumn = [])
    {
        $selectColumn = $this->getSelectColumns($selectColumn);

        $query = $this->getModel()->select($selectColumn);

        if (($faqInfoId = array_get($param, 'faq_info_id')) != null) {
            $query->FaqInfoIdQuery($faqInfoId);
        }

        if (($contents = array_get($param, 'contents')) != null) {
            $query->ContentsQuery($contents);
        }

        if (($contents = array_get($param, 'ids')) != null) {
            $query->IdsQuery($contents);
        }

        return $query;
    }

}