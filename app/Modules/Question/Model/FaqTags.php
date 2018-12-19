<?php

namespace App\Modules\Question\Model;

use App\Kernel\Traits\ModelTimeTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * 问答标签模型
 *
 * @author 秦昊
 * Date: 2018/8/31 11:01
 */
class FaqTags extends Model
{
    use SoftDeletes, ModelTimeTraits;

    /**
     * @var string
     */
    protected $table = 'faq_tags';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'add_time',
        'last_update_time',
    ];

    protected $casts = [
        'name'                      => 'string',
        'add_time'                  => 'integer',
        'last_update_time'          => 'integer',
        'created_at'                => 'timestamp',
        'updated_at'                => 'timestamp',
        'deleted_at'                => 'timestamp',
    ];

    /**
     * @var array
     */
    protected $appends = [
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * 根据问答标签名称模糊搜索
     *
     * @author 秦昊
     * Date: 2018/9/1 12:17
     * @param $query
     * @param $name
     * @return mixed
     */
    public function scopeNameQuery($query, $name)
    {
        return $query->where('name', 'LIKE', '%'.$name.'%');
    }

    /**
     * 问答信息与问答标签的关联关系
     * 多对多
     *
     * @author 秦昊
     * Date: 2018/9/1 21:46
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function FaqInfoList()
    {
        return $this->belongsToMany(
            \App\Modules\Question\Model\FaqInfo::class,
            'faq_relation_info_tags',
            'faq_tag_id',
            'faq_info_id'
        );
    }
}