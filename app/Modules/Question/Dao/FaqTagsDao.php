<?php


namespace App\Modules\Question\Dao;

use App\Modules\Question\Model\FaqTags;
use Illuminate\Database\Eloquent\Model;

/**
 * 问答标签数据处理
 *
 * @author 秦昊
 * Date: 2018/8/31 10:57
 */
class FaqTagsDao extends BaseDao
{
    protected $selectColumns = [
        'id',
        'name',
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
        return app(FaqTags::class);
    }

    /**
     * 获取问答标签分页列表
     *
     * @author 秦昊
     * Date: 2018/9/1 12:16
     * @param array $params
     * @param array $columns
     * @return mixed
     */
    public function getListPage(array $params = [], array $columns = [])
    {
        $pageSize = array_get($params, 'page_size', 10);

        $query = $this->doQuery($params, $columns);

        $query->OrderBy('last_update_time', 'DESC');
        $query->OrderBy('id',               'DESC');

        return $query->paginate($pageSize);
    }

    /**
     * 获取问答标签列表
     *
     * @author 秦昊
     * Date: 2018/9/11 14:55
     * @param array $params
     * @param array $columns
     * @return mixed
     */
    public function getList(array $params = [], array $columns = [])
    {
        $query = $this->doQuery($params, $columns);

        return $query->get();
    }

    /**
     * 构建筛选条件
     *
     * @author 秦昊
     * Date: 2018/8/31 10:51
     * @param array $param
     * @param array $selectColumn
     * @return mixed
     */
    protected function doQuery(array $param = [], array $selectColumn = [])
    {
        $selectColumn = $this->getSelectColumns($selectColumn);

        $query = $this->getModel()->select($selectColumn);

        if(($name = array_get($param, 'name')) != null)
        {
            $query->NameQuery($name);
        }

        return $query;
    }
}