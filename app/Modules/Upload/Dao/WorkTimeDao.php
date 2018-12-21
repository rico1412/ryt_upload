<?php

namespace App\Modules\Upload\Dao;

use App\Modules\Upload\Model\WorkTime;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @author 秦昊
 * Date: 2018/12/19 17:24
 */
class WorkTimeDao extends BaseDao
{
    /**
     * @author 秦昊
     * Date: 2018/12/19 17:28
     * @return Model
     */
    protected function getModel(): Model
    {
        return app(WorkTime::class);
    }

    /**
     * 根据项目代号查询
     *
     * @author 秦昊
     * Date: 2018/12/19 17:37
     * @param $bankCode
     * @return Model|null|object|static
     */
    public function findInfoByBankCode($bankCode)
    {
        return $this->doQuery(['bank_code' => $bankCode])->first();
    }

    /**
     * 构建查询条件
     *
     * @author 秦昊
     * Date: 2018/12/19 17:34
     * @param array $params
     * @param array $columns
     * @param array $withs
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function doQuery(array $params, $columns = [], $withs = [])
    {
        $query = $this->getModel()->newQuery();

        $query->select($this->getSelectColumns($columns));

        if ($withs) $query->with($withs);

        if (($bankCode = array_get($params, 'bank_code')) != null)
        {
            $query->BankCodeQuery($bankCode);
        }

        return $query;
    }
}