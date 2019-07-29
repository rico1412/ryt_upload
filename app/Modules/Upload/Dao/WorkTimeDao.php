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
     * 同步项目信息
     *
     * @author 秦昊
     * Date: 2018/12/21 23:22
     * @param array $newProjectList
     */
    public function syncProjects(array $newProjectList)
    {
        $storeList = [];

        foreach ($newProjectList as $bankCode => $projectInfo)
        {
            if ($oldProjectInfo = $this->findInfoByBankCode($bankCode))
            {
                $oldProjectInfo->fill($projectInfo);
                $oldProjectInfo->save();
            } else {
                $storeList[]    = $projectInfo;
            }
        }

        if ($storeList)
        {
            $this->insertMulti($storeList);
        }
    }

    /**
     * 根据项目代号查询
     *
     * @author 秦昊
     * Date: 2018/12/19 17:37
     * @param $bankCode
     * @return WorkTime|Model|null|object|static
     */
    public function findInfoByBankCode($bankCode)
    {
        return $this->doQuery(['bank_code' => $bankCode])->first();
    }

    /**
     * 获取所有项目信息
     *
     * @author 秦昊
     * Date: 2018/12/21 19:22
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getProjectList(array $columns = ['*'])
    {
        return $this->all($columns);
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
    public function doQuery(array $params, array $columns = [], array $withs = [])
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