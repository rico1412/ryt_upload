<?php


namespace App\Modules\Question\Dao;

use App\Modules\Question\Model\FaqRelationInfoTags;
use Illuminate\Database\Eloquent\Model;

/**
 * 问答信息与问答标签关系数据处理
 *
 * @author 秦昊
 * Date: 2018/8/31 10:58
 */
class FaqRelationInfoTagsDao extends BaseDao
{
    protected $selectColumns = [
        'id',
        'faq_info_id',
        'faq_tag_id',
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
        return app(FaqRelationInfoTags::class);
    }

    /**
     * 同步某问答信息的标签关联关系
     *
     * @author 秦昊
     * Date: 2018/8/31 14:04
     * @param $faqInfoId
     * @param array $newTagIds
     * @return array
     */
    public function syncInfoRelations($faqInfoId, array $newTagIds)
    {
        // 查询当前标签关系
        $currentRelationList = $this->getTagIdListByPid($faqInfoId, ['id', 'faq_tag_id']);

        // 当前标签关系数组
        $currentTagIds = array_pluck($currentRelationList->toArray(), 'faq_tag_id');

        $storeTagIdArr = array_diff($newTagIds, $currentTagIds);
        $delTagIdArr = array_diff($currentTagIds, $newTagIds);

        if(!empty($delTagIdArr)){
            $this->delSomeFaqRelationTags($faqInfoId, $delTagIdArr);
        }

        $this->storeMultiFaqRelationTags($faqInfoId, $storeTagIdArr);

        return [
            'store'  => $storeTagIdArr,
            'delete' => $delTagIdArr,
        ];
    }

    /**
     * 根据问答标签删除关联关系
     *
     * @author 秦昊
     * Date: 2018/9/19 10:19
     * @param $faqTagId
     * @return mixed
     */
    public function delTagRelations($faqTagId)
    {
        return $this->doQuery(['faq_tag_id' => $faqTagId])->forceDelete();
    }

    /**
     * 根据问答信息id获取其对应的问答标签id列表
     *
     * @author 秦昊
     * Date: 2018/8/31 13:50
     * @param $faqInfoId
     * @param array $columns
     * @return mixed
     */
    public function getTagIdListByPid($faqInfoId, array $columns = [])
    {
        return $this->doQuery(['faq_info_id' => $faqInfoId], $columns)->get();
    }

    /**
     * 删除某问答信息的一些标签关联关系
     *
     * @author 秦昊
     * Date: 2018/8/31 14:03
     * @param $faqInfoId
     * @param array $faqTagIds
     * @return mixed
     */
    public function delSomeFaqRelationTags($faqInfoId, array $faqTagIds)
    {
        if (!empty($faqTagIds)) {// 必须判空，不然会删除所有
            return $this->doQuery([
                            'faq_info_id' => $faqInfoId,
                            'faq_tag_ids' => $faqTagIds
                        ])->forceDelete();
        }

        return null;
    }

    /**
     * 删除 某问答的标签关系
     * @param $faqInfoId
     * @return mixed
     */
    public function deleteByFaqInfoId($faqInfoId)
    {
        return $this->doQuery(['faq_info_id' => $faqInfoId])->forceDelete();
    }

    /**
     * 添加多个问答信息与问答标签的关联关系
     *
     * @author 秦昊
     * Date: 2018/8/31 14:02
     * @param $faqInfoId
     * @param array $faqTagIds
     * @return bool
     */
    public function storeMultiFaqRelationTags($faqInfoId, array $faqTagIds)
    {
        $addRelations = [];

        foreach ($faqTagIds as $faqTagId) {
            if (!empty($faqTagId)){
                $addRelations[] = [
                    'faq_info_id'       => $faqInfoId,
                    'faq_tag_id'        => $faqTagId,
                    'add_time'          =>  get_now(),
                    'last_update_time'  =>  get_now(),
                ];
            }
        }

        return $this->insertMulti($addRelations);
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

        if (($faqTagId = array_get($param, 'faq_tag_id')) != null) {
            $query->FaqTagIdQuery($faqTagId);
        }

        if (($faqTagIds = array_get($param, 'faq_tag_ids')) != null) {
            $query->FaqTagIdsQuery($faqTagIds);
        }

        return $query;
    }

}