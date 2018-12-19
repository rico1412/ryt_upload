<?php

namespace App\Modules\Question\Dao;

use App\Modules\Question\Model\FaqInfo;
use Illuminate\Database\Eloquent\Model;

/**
 * 问答数据处理
 *
 * @author kkk
 */
class FaqInfoDao extends BaseDao
{
    protected $selectColumns = [
        'id',
        'title',
        'description',
        'copy_total_count',
        'best_answer_id',
        'best_answer_copy_count',
        'best_answer_content',
        'search',
        'add_time',
        'last_update_time',
        'last_edit_time',
    ];

    /**
     * Get model.
     *
     * @return Model
     */
    protected function getModel(): Model
    {
        return app(FaqInfo::class);
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
    public function getListPage(array $params = [], array $columns = [])
    {
        $pageSize = get_page_size($params);

        $query = $this->doQuery($params, $columns);

        $query->DefaultSort();

        return $query->paginate($pageSize);
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
    public function getAppListPage(array $params = [], array $columns = [])
    {
        $pageSize = get_page_size($params);

        $query = $this->doQuery($params, $columns);

        $query->orderBy('copy_total_count', 'DESC');

        $query->DefaultSort();

        return $query->paginate($pageSize);
    }

    /**
     * 更新search字段
     *
     * @author 秦昊
     * Date: 2018/9/4 17:47
     * @param $id
     * @param $params
     * @return mixed
     */
    public function updateById($id, array $params = [])
    {
        return $this->getModel()->IdQuery($id)->update($params);
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

        if(($title = array_get($param, 'title')) != null)
        {
            $query->TitleQuery($title);
        }

        if(($search = array_get($param, 'search')) != null)
        {
            $query->SearchQuery($search);
        }

        return $query;
    }
    
}