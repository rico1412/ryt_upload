<?php


namespace App\Modules\Question\Business;

use App\Exceptions\AppException;
use App\Kernel\Base\BaseBusiness;
use App\Modules\Question\Dao\FaqInfoDao;
use App\Modules\Question\Dao\FaqRelationInfoTagsDao;
use App\Modules\Question\Dao\FaqTagsDao;
use App\Modules\Question\Invoke\QuestionInvoke;

/**
 *
 *
 * @author 秦昊
 * Date: 2018/9/1 10:15
 */
class FaqTagBusiness extends BaseBusiness
{
    protected $faqInfoDao               = null;
    protected $faqTagsDao               = null;
    protected $faqRelationInfoTagsDao   = null;
    protected $questionInvoke           = null;

    public function __construct(
        FaqInfoDao              $faqInfoDao,
        FaqTagsDao              $faqTagsDao,
        FaqRelationInfoTagsDao  $faqRelationInfoTagsDao,
        QuestionInvoke          $questionInvoke
    )
    {
        $this->faqInfoDao               = $faqInfoDao;
        $this->faqTagsDao               = $faqTagsDao;
        $this->faqRelationInfoTagsDao   = $faqRelationInfoTagsDao;
        $this->questionInvoke           = $questionInvoke;
    }

    /**
     * 获取问答标签列表
     *
     * @author 秦昊
     * Date: 2018/9/1 12:16
     * @param array $params
     * @param array $columns
     * @return mixed
     */
    public function list(array $params, array $columns = [])
    {
        app('validator')->make($params, [
            'name'      => 'string|max:6',
        ])->validate();

        $faqTagList = $this->faqTagsDao->getList($params, $columns);

        return $faqTagList;
    }

    /**
     * 获取问答标签分页列表
     *
     * @author 秦昊
     * Date: 2018/9/11 14:55
     * @param array $params
     * @param array $columns
     * @return mixed
     */
    public function pageList(array $params, array $columns = [])
    {
        app('validator')->make($params, [
            'name'      => 'string|max:6',
        ])->validate();

        $faqTagList = $this->faqTagsDao->getListPage($params, $columns);

        return $faqTagList;
    }

    /**
     * 添加问题标签
     *
     * @author 秦昊
     * Date: 2018/9/1 12:53
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function add(array $params)
    {
        app('validator')->make($params, [
            'name' => 'required|string|max:6|unique:faq_tags,name,0,id,deleted_at,NULL',
        ])->validate();

        return $this->faqTagsDao->store($params);
    }

    /**
     * 删除问题标签及对应的关联关系
     *
     * @author 秦昊
     * Date: 2018/9/7 15:26
     * @param $id
     * @return mixed
     * @throws AppException
     */
    public function del($id)
    {
        if (!is_numeric($id)) {
            throw new AppException(100003);
        }

        return app('db')->transaction(function () use($id) {
            // 在删除标签和关系之前获取问答信息，不然就获取不到了
            $faqInfoList    = $this->faqTagsDao->findOrFail($id)->FaqInfoList;

            // 根据标签id删除问题标签
            $this->faqTagsDao->destroy($id);

            // 删除问题信息与问题标签的关联关系
            $this->faqRelationInfoTagsDao->delTagRelations($id);

            // 更新问答信息的查询字段
            foreach ($faqInfoList as $faqInfo) {
                $this->faqInfoDao->updateById($faqInfo->id,
                    ['search' => $this->questionInvoke->syncData($faqInfo)]
                );
            }
        }, 2);
    }

}