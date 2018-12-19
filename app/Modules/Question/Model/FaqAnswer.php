<?php


namespace App\Modules\Question\Model;

use App\Kernel\Traits\ModelTimeTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 问答答案模型
 *
 * @author 秦昊
 * Date: 2018/8/31 14:22
 */
class FaqAnswer extends Model
{
    use SoftDeletes, ModelTimeTraits;

    /**
     * @var string
     */
    protected $table = 'faq_answer';

    /**
     * @var array
     */
    protected $fillable = [
        'faq_info_id',
        'content',
        'copy_count',
        'add_time',
        'last_update_time',
    ];

    protected $casts = [
        'faq_info_id'               => 'integer',
        'content'                   => 'string',
        'copy_count'                => 'integer',
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
        'new_content'
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
     * 追加返回格式化后的内容数据
     *
     * @author 秦昊
     * Date: 2018/9/5 15:41
     * @return mixed
     */
    public function getNewContentAttribute()
    {
        return format_content($this->content);
    }

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
     * 根据答案内容查找
     *
     * @author 秦昊
     * Date: 2018/8/31 14:55
     * @param $query
     * @param $contents
     * @return mixed
     */
    public function scopeContentsQuery($query, $contents)
    {
        return $query->whereIn('content', $contents);
    }

    /**
     * 根据答案id查找
     *
     * @author 秦昊
     * Date: 2018/9/11 10:42
     * @param $query
     * @param $ids
     * @return mixed
     */
    public function scopeIdsQuery($query, $ids)
    {
        return $query->whereIn('id', $ids);
    }

    /**
     * 问答答案与其问答信息的关联关系
     * 多对一
     *
     * @author 秦昊
     * Date: 2018/9/2 23:08
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function FaqInfo()
    {
        return $this->belongsTo(
            \App\Modules\Question\Model\FaqInfo::class,
            'faq_info_id',
            'id'
        );
    }

}