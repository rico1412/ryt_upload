<?php


namespace App\Modules\Question\Model;

use App\Kernel\Traits\ModelTimeTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 问答信息与问答标签关系模型
 *
 * @author 秦昊
 * Date: 2018/8/31 11:01
 */
class FaqRelationInfoTags extends Model
{
    use SoftDeletes, ModelTimeTraits;

    /**
     * @var string
     */
    protected $table = 'faq_relation_info_tags';

    /**
     * @var array
     */
    protected $fillable = [
        'faq_info_id',
        'faq_tag_id',
        'add_time',
        'last_update_time',
    ];

    protected $casts = [
        'faq_info_id'               => 'integer',
        'faq_tag_id'                => 'integer',
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
     * 根据问答信息id搜索
     *
     * @author 秦昊
     * Date: 2018/8/31 11:43
     * @param $query
     * @param $faqInfoId
     * @return mixed
     */
    public function scopeFaqInfoIdQuery($query, $faqInfoId)
    {
        return $query->where('faq_info_id', $faqInfoId);
    }

    /**
     * 根据问答标签搜索
     *
     * @author 秦昊
     * Date: 2018/8/31 11:43
     * @param $query
     * @param $faqTagId
     * @return mixed
     */
    public function scopeFaqTagIdQuery($query, $faqTagId)
    {
        return $query->where('faq_tag_id', $faqTagId);
    }

    /**
     * 根据问答标签搜索
     *
     * @author 秦昊
     * Date: 2018/8/31 11:43
     * @param $query
     * @param $faqTagIds
     * @return mixed
     */
    public function scopeFaqTagIdsQuery($query, array $faqTagIds)
    {
        return $query->whereIn('faq_tag_id', $faqTagIds);
    }

}