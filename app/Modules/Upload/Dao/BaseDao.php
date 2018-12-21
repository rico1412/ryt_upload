<?php


namespace App\Modules\Upload\Dao;

use App\Exceptions\FaqInfoException;
use App\Kernel\Base\BaseDao as Dao;

/**
 * 数据处理基类
 *
 * @author 秦昊
 */
abstract class BaseDao extends Dao
{
    /**
     * @var Builder
     */
    protected $query;

    /**
     * Add data
     *
     * @param array $params
     * @param array $extra
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(array $params, array $extra = [])
    {
        $model = $this->getModel();

        $model->fill($params);
        $model->forceFill($extra);
        $model->save();

        return $model;
    }

    /**
     * Add datas
     *
     * User: 秦昊
     * Date: 2018/8/23 21:00
     * @param array $datas
     * @return bool
     */
    public function insertMulti(array $datas): bool
    {
        return $this->getModel()->insert($datas);
    }

    /**
     * Get data by id or throw exception
     *
     * @author 秦昊
     * Date: 2018/8/31 15:15
     * @param $id
     * @param array $columns
     * @return mixed
     * @throws FaqInfoException
     */
    public function findOrFail($id, array $columns = [])
    {
        if ($model = $this->find($id, $columns)) return $model;

        throw new FaqInfoException(500002);
    }

    /**
     * Update data by id
     *
     * @author 秦昊
     * Date: 2018/8/31 15:15
     * @param $id
     * @param array $params
     * @param array $extra
     * @return mixed
     * @throws FaqInfoException
     */
    public function update($id, array $params = [], array $extra = [])
    {
        $model = $this->findOrFail($id);

        $model->fill($params);
        $model->forceFill($extra);
        $model->save();

        return $model;
    }

    /**
     * @author 秦昊
     * Date: 2018/12/21 19:22
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all(array $columns = ['*'])
    {
        return $this->getModel()->all($columns);
    }

    /**
     * 查询数据.
     *
     * @param array $params
     * @param array $columns
     * @param array $withs
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function get(array $params, array $columns = [], array $withs = [])
    {
        return $this->query($params, $columns, $withs)->get();
    }

    /**
     * Query builder.
     *
     * @param array $params
     * @param array $columns
     * @param array $withs
     * @return \Illuminate\Database\Eloquent\Builder
     *
     */
    public function query(array $params, array $columns = [], array $withs = [])
    {
        $this->query = $this->getModel()->newQuery();

        $this->query->select($this->getSelectColumns($columns));

        if ($withs) $this->query->with($withs);

        foreach ($params as $k => $v)
        {
            $func = camel_case('query_' . $k);

            if (method_exists($this, $func))
            {
                $this->$func($v);
            }
        }

        return $this->query;
    }

    /**
     * Order by query result
     *
     * @param array $items
     */
    public function queryOrderBy(array $items)
    {
        foreach ($items as $item)
        {
            list ($column, $way) = $item;

            $this->query->orderBy($column, $way);
        }
    }

    /**
     * Limit query result.
     *
     * @param $limit
     */
    public function queryLimit($limit)
    {
        if ($limit)
        {
            $this->query->limit($limit);
        }
    }

    /**
     * Query start id
     *
     * @param $startId
     */
    public function queryStartId($startId)
    {
        if ($startId)
        {
            $this->query->where('id', '<', $startId);
        }
    }

    /**
     * Query sort
     *
     * @param $sort
     */
    public function querySort($sort)
    {
        if ($sort)
        {
            $this->query->where('sort', '<', $sort);
        }
    }
}