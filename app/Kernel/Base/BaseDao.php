<?php

namespace App\Kernel\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * 数据处理基类
 *
 * @author 51004
 */
abstract class BaseDao
{
    /**
     * @var array
     */
    protected $selectColumns = ['*'];
    
    /**
     * @return Model
     */
    abstract protected function getModel() : Model;

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, array $columns = [])
    {
        return $this->getModel()->find($id, $this->getSelectColumns($columns));
    }

    /**
     * @param $query
     * @param array $sorts
     * @return mixed
     */
    public function sorts($query, array $sorts)
    {
        return tap($query, function ($query) use ($sorts)
        {
            foreach ($sorts as $sort)
            {
                list ($column, $way) = $sort;

                $query = $query->orderBy($column, $way);
            }
        });
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        return $this->getModel()->destroy($id);
    }

    /**
     * @author 秦昊
     * Date: 2018/8/31 18:31
     * @param $id
     * @return bool|null
     */
    public function forceDeleteById($id)
    {
        return $this->getModel()->find($id)->forceDelete();
    }

    /**
     * @param array $columns
     * @return array
     */
    public function getSelectColumns(array $columns = []) : array
    {
        return $columns ?: $this->selectColumns;
    }
}